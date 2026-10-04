#!/usr/bin/env bash
# ==============================================================================
# Deterministic Conventions & Code Style Checker — io.edu LMS
#
# Validates code against GEMINI.md, AGENTS.md, and CI3 Architecture Skills.
# Checks:
# 1. No Vertical Alignment (spaces before =>)
# 2. No Single-Letter Variables ($i, $k, $v, etc.)
# 3. Indentation via Tabs (no spaces at beginning of lines)
# 4. PSR-12 Braces ({ on next line for classes and functions)
# 5. No Input XSS Filtering ($this->input->post('...', TRUE))
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

	# Skip third-party/generated files if any
	if [[ "$file" == *"vendor/"* ]] || [[ "$file" == *"system/"* ]]; then
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

	if [ "$FILE_VIOLATIONS" -gt 0 ]; then
		TOTAL_VIOLATIONS=$((TOTAL_VIOLATIONS + FILE_VIOLATIONS))
	fi
done

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
