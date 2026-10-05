TITULO: Trava Determinística de Ações do Agente de Implementação (Git Safety Gate)

STATUS: CONCLUÍDO
DEPENDÊNCIA: Nenhuma

DESCRIÇÃO:
Implementar uma trava determinística (bloqueio programático rígido) para impedir que agentes de desenvolvimento (como o `/code-implementer` ou interações em prompt livre) realizem commits, pushes ou encenações (`git add`) no repositório de forma autônoma ou alucinatória.

O versionamento de código e envio ao repositório remoto são atribuições EXCLUSIVAS do agente especializado `/git-committer` (ou ações manuais do desenvolvedor no terminal).

---

### ARQUITETURA DA SOLUÇÃO (DEFESA DETERMINÍSTICA):

1. **Antigravity Lifecycle Hook (`.agents/hooks.json`)**:
   - Registra o hook `PreToolUse` para interceptar toda e qualquer chamada à ferramenta `run_command` da IDE.
   - Aponta para o script guardião `.agents/scripts/git-guard.py`.

2. **Script Guardião de Execução (`.agents/scripts/git-guard.py`)**:
   - Intercepta comandos de terminal antes de serem repassados ao bash do sistema operacional.
   - Detecta subcomandos mutantes do Git: `commit`, `push`, `add`, `merge`, `rebase`, `cherry-pick`, `tag`, `reset`.
   - Permite irrestritamente comandos de leitura: `status`, `diff`, `log`, `show`, `branch`, etc., além de comandos não-git (Docker, PHPUnit, etc.).
   - Valida autorização através de:
     - Assinatura explícita de autenticação (`GIT_AGENT=git-committer`).
     - Inspeção do `transcript.jsonl` para verificar se o usuário invocou o workflow `/git-committer`.
   - Caso um comando mutante seja disparado sem autorização, emite resposta determinística:
     `{"decision": "deny", "reason": "[TRAVA DETERMINÍSTICA - IOEDU-0016] ..."}` abortando a chamada na IDE.

3. **Alinhamento do Agente de Versionamento (`.agents/workflows/git-committer.md`)**:
   - Permissões de bash atualizadas para incluir `GIT_AGENT=git-committer git-leandro *` e `git-leandro push*`.
   - Instrução obrigatória para prefixar comandos de versionamento com `GIT_AGENT=git-committer`.

4. **Centralização de Scripts em `.agents/scripts/`**:
   - `bin/check-conventions.sh` movido para `.agents/scripts/check-conventions.sh`.
   - Pasta `bin/` removida da raiz do projeto.
   - Referências e permissões no `.agents/workflows/code-reviewer.md` atualizadas.
   - Todos os utilitários de agentes e automação agora residem centralizados sob `.agents/scripts/`.

5. **Preservação de Regras Globais (`AGENTS.md` e `GEMINI.md`)**:
   - As instruções globais de uso do `git-leandro` permanecem intactas para orientar desenvolvedores e ações manuais.

---

### CRITÉRIOS DE ACEITE:
- [x] Criação de `.agents/scripts/git-guard.py` com validação determinística de comandos Git.
- [x] Configuração de `.agents/hooks.json` com `PreToolUse` ativo para `run_command`.
- [x] Testes unitários do guardião cobrindo comandos permitidos (`status`, `diff`, `log`, `phpunit`), comandos bloqueados (`commit`, `push`, `add`), encadeamento (`&&`) e autorização com token.
- [x] Atualização das instruções de execução no `.agents/workflows/git-committer.md`.
- [x] Ações de commit/push bloqueadas automaticamente caso o `/code-implementer` tente executá-las.
- [x] Centralização de scripts de automação dos agentes em `.agents/scripts/` e remoção da pasta `bin/`.