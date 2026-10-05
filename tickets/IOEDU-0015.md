TITULO: [FASE 2] Composição Avançada de Conteúdo e Aulas Exclusivas por Turma

STATUS: BACKLOG_FUTURO
DEPENDÊNCIA: IOEDU-0012, IOEDU-0014

DESCRIÇÃO:
Implementar o recurso de personalização de grade pedagógica para turmas específicas. Por padrão, qualquer turma criada utiliza integralmente o conteúdo base do curso (IOEDU-0012). Este ticket implementa a possibilidade avançada de uma turma específica ocultar determinados módulos/aulas do curso ou adicionar módulos e aulas exclusivas (ex: aulas ao vivo gravadas daquela edição, plantões de dúvidas ou materiais extras específicos para aquele grupo).

---

### ARQUITETURA DE MATRIZ DE CONTEÚDO (SEM DUPLICAÇÃO):
1. **Padrão Sem Customização**:
   - Se a turma não possui registros customizados em `class_modules` / `class_lessons`, o sistema assume herança dinâmica direta de 100% do curso base.
2. **Customização Sob Demanda**:
   - Somente quando o administrador decide customizar a grade daquela turma é que a matriz é acionada:
     - `class_modules` (aponta para `course_modules.id` ou módulo exclusivo da turma).
     - `class_lessons` (aponta para `course_lessons.id` ou aula exclusiva da turma).
   - O conteúdo base do curso (vídeos, textos) nunca é duplicado.

---

### CRITÉRIOS DE ACEITE:
- [ ] Turmas herdam automaticamente todo o conteúdo do curso sem necessidade de configuração manual prévia.
- [ ] Painel opcional na Turma para customizar a grade: marcar aulas que ficam ocultas para aquela turma.
- [ ] Possibilidade de adicionar aulas exclusivas na turma sem refletir no curso base.
- [ ] Testes unitários para composição da grade da turma (cobertura >= 80%).
