#!/usr/bin/env bash
# ==============================================================================
# Deterministic Conventions & Code Style Checker — io.edu LMS
#
# Validates code against GEMINI.md, AGENTS.md, and CI3 Architecture Skills.
# Checks:
# 1. No Vertical Alignment (spaces before =>)
# 2. Indentation via Tabs (no spaces at beginning of lines)
# 3. PSR-12 Braces ({ on next line for classes and functions)
# 4. No Single-Letter Variables ($i, $k, $v, etc.)
# 5. No Input XSS Filtering ($this->input->post('...', TRUE))
# 6. No Manual Try-Catch in Controllers
# 7. No Native/Non-AppException in Domain and Use Cases (Check A)
# 8. No Literal Default Integer Parameters in Controllers (Check B)
# 9. No Direct Model Persistence Calls in Controllers (Check C)
# 10. No Antipattern Hydration Methods in Domain Entities (Check D)
# 11. Mandatory Form Validation in Controller Mutation Actions (Check F)
# 12. RBAC Route Coverage Verification (Check E via .agents/scripts/verify-rbac-routes.php)
# 13. Block Types & Child Table Persistence (Check G via .agents/scripts/verify-block-types.php)
# ==============================================================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

TOTAL_VIOLATIONS=0
CHECKED_FILES=0

get_files_to_check() {
	if [ "$1" == "--staged" ]; then
		if command -v git-leandro &>/dev/null; then
			git-leandro --no-pager diff --name-only --cached -- 'application/*.php' 'tests/*.php'
		else
			git --no-pager diff --name-only --cached -- 'application/*.php' 'tests/*.php'
		fi
	elif [ "$#" -gt 0 ]; then
		for f in "$@"; do
			if [[ "$f" == *.php ]] && [[ -f "$f" ]]; then
				echo "$f"
			fi
		done
	else
		find application/ -type f -name '*.php'
	fi
}

echo -e "${BLUE}================================================================${NC}"
echo -e "${BLUE}         io.edu — Deterministic Conventions Validator         ${NC}"
echo -e "${BLUE}================================================================${NC}"

FILES=$(get_files_to_check "$@")

if [ -z "$FILES" ]; then
	echo -e "${YELLOW}Nenhum arquivo PHP encontrado para verificação.${NC}"
	exit 0
fi

for file in $FILES; do
	if [ ! -f "$file" ]; then
		continue
	fi

	# Skip third-party/generated/runtime files
	if [[ "$file" == *"vendor/"* ]] || [[ "$file" == *"system/"* ]] || \
	   [[ "$file" == *"application/logs/"* ]] || [[ "$file" == *"application/cache/"* ]] || \
	   [[ "$file" == *"application/migrations/"* ]] || \
	   [[ "$file" == *"config/user_agents.php"* ]] || [[ "$file" == *"config/mimes.php"* ]] || \
	   [[ "$file" == *"config/smileys.php"* ]] || [[ "$file" == *"config/doctypes.php"* ]] || \
	   [[ "$file" == *"config/foreign_chars.php"* ]] || [[ "$file" == *"config/memcached.php"* ]]; then
		continue
	fi

	FILE_VIOLATIONS=0
	CHECKED_FILES=$((CHECKED_FILES + 1))

	# 1. Check Vertical Alignment (\s{2,}=>)
	ALIGNMENT_MATCHES=$(grep -Hn -P -- '\s{2,}=>' "$file" || true)
	if [ -n "$ALIGNMENT_MATCHES" ]; then
		echo -e "\n${RED}[FALHA] Alinhamento Vertical Detectado em:${NC} $file"
		echo "$ALIGNMENT_MATCHES" | while read -r line; do
			echo -e "   ${YELLOW}Linha:${NC} $line"
		done
		FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
	fi

	# 2. Check Spaces for Indentation instead of Tabs
	SPACE_INDENT_MATCHES=$(grep -Hn -P '^[ ]{2,}[^ \*]' "$file" || true)
	if [ -n "$SPACE_INDENT_MATCHES" ]; then
		echo -e "\n${RED}[FALHA] Indentação com Espaços em vez de Tabs em:${NC} $file"
		echo "$SPACE_INDENT_MATCHES" | head -n 5 | while read -r line; do
			echo -e "   ${YELLOW}Linha:${NC} $line"
		done
		FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
	fi

	# 3. Check PSR-12 Opening Braces on Same Line
	BRACE_MATCHES=$(grep -Hn -P '^\s*(public|protected|private|final|abstract)?\s*(static\s+)?(function|class|interface|trait)\s+[a-zA-Z0-9_]+.*\{\s*$' "$file" || true)
	if [ -n "$BRACE_MATCHES" ]; then
		echo -e "\n${RED}[FALHA] Abertura de Chave na Mesma Linha (PSR-12 exige na próxima) em:${NC} $file"
		echo "$BRACE_MATCHES" | while read -r line; do
			echo -e "   ${YELLOW}Linha:${NC} $line"
		done
		FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
	fi

	# 4. Check Single-Letter Variables ($i, $u, $k, etc.)
	SINGLE_VAR_MATCHES=$(grep -Hn -P '(?<![a-zA-Z0-9_\$])\$[a-zA-Z](?![a-zA-Z0-9_])' "$file" | grep -v '^\s*//' | grep -v '^\s*\*' || true)
	if [ -n "$SINGLE_VAR_MATCHES" ]; then
		echo -e "\n${RED}[FALHA] Variável de Letra Única Detectada (proibido por GEMINI.md) em:${NC} $file"
		echo "$SINGLE_VAR_MATCHES" | while read -r line; do
			echo -e "   ${YELLOW}Linha:${NC} $line"
		done
		FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
	fi

	# 5. Check Input XSS Filter ($this->input->post('...', TRUE))
	INPUT_XSS_MATCHES=$(grep -Hn -P -- '->input->(post|get|cookie)\([^,]+,\s*TRUE\)' "$file" || true)
	if [ -n "$INPUT_XSS_MATCHES" ]; then
		echo -e "\n${RED}[FALHA] Input XSS Filter Ativado (\$this->input->... TRUE é proibido) em:${NC} $file"
		echo "$INPUT_XSS_MATCHES" | while read -r line; do
			echo -e "   ${YELLOW}Linha:${NC} $line"
		done
		FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
	fi

	# 6. Check Manual Try-Catch in Controllers (forbidden: exceptions must bubble to MY_Controller::_remap)
	if [[ "$file" == *"application/controllers/"* ]]; then
		TRY_CATCH_MATCHES=$(grep -Hn -P '^\s*catch\s*\(' "$file" || true)
		if [ -n "$TRY_CATCH_MATCHES" ]; then
			echo -e "\n${RED}[FALHA] Bloco try-catch manual detectado em Controller (exceções devem ser tratadas globalmente por MY_Controller::_remap) em:${NC} $file"
			echo "$TRY_CATCH_MATCHES" | while read -r line; do
				echo -e "   ${YELLOW}Linha:${NC} $line"
			done
			FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
		fi
	fi

	# 7. Check Native/Non-AppException in Domain and Use Cases (Check A)
	if [[ "$file" == *"application/domain/"* ]] || [[ "$file" == *"application/usecases/"* ]]; then
		if [[ "$file" != *"AppException.php"* ]] && [[ "$file" != *"/value_objects/"* ]]; then
			NATIVE_EX_MATCHES=$(grep -Hn -P 'throw\s+new\s+\\?(InvalidArgumentException|RuntimeException|Exception|LogicException|BadMethodCallException|UnexpectedValueException|DomainException)\b' "$file" || true)
			if [ -n "$NATIVE_EX_MATCHES" ]; then
				echo -e "\n${RED}[FALHA - Check A] Lançamento de Exceção Nativa/Não-AppException em:${NC} $file"
				echo -e "   ${YELLOW}(Exceções de Domínio/Use Case devem herdar de AppException para evitar HTTP 500 no MY_Controller::_remap)${NC}"
				echo "$NATIVE_EX_MATCHES" | while read -r line; do
					echo -e "   ${YELLOW}Linha:${NC} $line"
				done
				FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
			fi
		fi
	fi

	# 8. Check Dangerous Literal Integer Defaults in Controller Parameters (Check B)
	if [[ "$file" == *"application/controllers/"* ]]; then
		PARAM_DEFAULT_MATCHES=$(grep -Hn -P 'public\s+function\s+[a-zA-Z0-9_]+\s*\([^)]*=\s*[1-9][0-9]*' "$file" || true)
		if [ -n "$PARAM_DEFAULT_MATCHES" ]; then
			echo -e "\n${RED}[FALHA - Check B] Valor Padrão Inteiro Literal Detectado em Parâmetro de Controller em:${NC} $file"
			echo -e "   ${YELLOW}(Métodos públicos de controller não devem assumir ID default como '= 1'; use '= null')${NC}"
			echo "$PARAM_DEFAULT_MATCHES" | while read -r line; do
				echo -e "   ${YELLOW}Linha:${NC} $line"
			done
			FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
		fi
	fi

	# 9. Check Direct Model Persistence Calls in Controllers (Check C)
	if [[ "$file" == *"application/controllers/"* ]]; then
		MODEL_CALL_MATCHES=$(grep -Hn -P '\$(this->)?[a-zA-Z0-9_]*(model|Model)->(insert|update|delete|destroy|save|insert_many|update_many)\(' "$file" || true)
		if [ -n "$MODEL_CALL_MATCHES" ]; then
			echo -e "\n${RED}[FALHA - Check C] Chamada Direta a Método de Persistência de Model em Controller em:${NC} $file"
			echo -e "   ${YELLOW}(Controllers devem delegar todas as operações de mutação para Use Cases)${NC}"
			echo "$MODEL_CALL_MATCHES" | while read -r line; do
				echo -e "   ${YELLOW}Linha:${NC} $line"
			done
			FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
		fi
	fi

	# 10. Check Antipattern Hydration Methods in Domain Entities (Check D)
	if [[ "$file" == *"application/domain/"* ]]; then
		HYDRATION_MATCHES=$(grep -Hn -P 'function\s+(from_database|reconstitute|to_database)\b' "$file" || true)
		if [ -n "$HYDRATION_MATCHES" ]; then
			echo -e "\n${RED}[FALHA - Check D] Método Antipattern de Hidratação Detectado em Domínio em:${NC} $file"
			echo -e "   ${YELLOW}(Entidades de Domínio devem usar apenas create(); mapeamento é responsabilidade dos Mappers)${NC}"
			echo "$HYDRATION_MATCHES" | while read -r line; do
				echo -e "   ${YELLOW}Linha:${NC} $line"
			done
			FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
		fi
	fi

	# 11. Check Missing Form Validation in Controller Mutation Actions (Check F)
	if [[ "$file" == *"application/controllers/"* ]]; then
		FV_VIOLATIONS=$(php -r '
			$file = $argv[1];
			$content = file_get_contents($file);
			$tokens = token_get_all($content);
			$count = count($tokens);
			$current_function = null;
			$brace_depth = 0;
			$body = "";

			for ($index = 0; $index < $count; $index++) {
				$token = $tokens[$index];
				if (is_array($token) && $token[0] === T_FUNCTION) {
					$lookahead = $index + 1;
					while ($lookahead < $count && is_array($tokens[$lookahead]) && in_array($tokens[$lookahead][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
						$lookahead++;
					}
					if ($lookahead < $count && is_array($tokens[$lookahead]) && in_array($tokens[$lookahead][1], ["save", "create", "update"])) {
						$current_function = $tokens[$lookahead][1];
						$brace_depth = 0;
						$body = "";
					}
				}
				if ($current_function !== null) {
					if ($token === "{") {
						$brace_depth++;
					} elseif ($token === "}") {
						$brace_depth--;
						if ($brace_depth === 0) {
							$has_input = str_contains($body, "get_input(") || str_contains($body, "input->post(") || str_contains($body, "input->get_post(");
							$has_fv = str_contains($body, "form_validation->run()");
							if ($has_input && !$has_fv) {
								echo "Método {$current_function}() recebe entrada do usuário mas não executa form_validation->run()\n";
							}
							$current_function = null;
						}
					} elseif ($brace_depth > 0) {
						$body .= is_array($token) ? $token[1] : $token;
					}
				}
			}
		' "$file" 2>/dev/null || true)
		if [ -n "$FV_VIOLATIONS" ]; then
			echo -e "\n${RED}[FALHA - Check F] Ação de Escrita sem Form Validation em:${NC} $file"
			echo -e "   ${YELLOW}$FV_VIOLATIONS${NC}"
			FILE_VIOLATIONS=$((FILE_VIOLATIONS + 1))
		fi
	fi

	if [ "$FILE_VIOLATIONS" -gt 0 ]; then
		TOTAL_VIOLATIONS=$((TOTAL_VIOLATIONS + FILE_VIOLATIONS))
	fi
done

# Run structural checks when inspecting the whole project
if [ "$#" -eq 0 ] || [ "$1" == "--all" ]; then
	echo -e "\n${BLUE}================================================================${NC}"
	echo -e "${BLUE}        Verificações Estruturais e de Integração Sistêmica        ${NC}"
	echo -e "${BLUE}================================================================${NC}\n"

	# Use Cases 1:1 Test Verifier
	if [ -f ".agents/scripts/verify-usecase-tests.php" ]; then
		if ! php .agents/scripts/verify-usecase-tests.php; then
			TOTAL_VIOLATIONS=$((TOTAL_VIOLATIONS + 1))
		fi
	fi

	# RBAC Route Coverage Verifier (Check E)
	if [ -f ".agents/scripts/verify-rbac-routes.php" ]; then
		if ! php .agents/scripts/verify-rbac-routes.php; then
			TOTAL_VIOLATIONS=$((TOTAL_VIOLATIONS + 1))
		fi
	fi

	# Block Types Persistence Verifier (Check G)
	if [ -f ".agents/scripts/verify-block-types.php" ]; then
		if ! php .agents/scripts/verify-block-types.php; then
			TOTAL_VIOLATIONS=$((TOTAL_VIOLATIONS + 1))
		fi
	fi
fi

echo ""
echo -e "${BLUE}================================================================${NC}"
echo -e "Arquivos verificados: ${CHECKED_FILES}"

if [ "$TOTAL_VIOLATIONS" -eq 0 ]; then
	echo -e "${GREEN}✓ SUCESSO: Todos os arquivos cumprem as convenções do GEMINI.md!${NC}"
	echo -e "${BLUE}================================================================${NC}"
	exit 0
else
	echo -e "${RED}✗ ERRO: Foram encontradas ${TOTAL_VIOLATIONS} categorias de violação!${NC}"
	echo -e "${BLUE}================================================================${NC}"
	exit 1
fi
