TITULO: Realizar limpeza e organização

DESCRIÇÃO:
1. Deletar controller Welcome.php
2. Deletear controllers Classes.php, Courses.php, Video_providers.php, deletar todos os arquivos que surgiram apartir desses arquivos.
2.1 Os arquivos são: Category_model.php, Class_lesson_model.php, Course_model.php, Lesson_model.php, Module_model.php, Section_model.php e Video_provider_model.php, o arquivo Class_model deve ser excluído também.
2.2 Deletar os arquivos em usecases/education/*
2.3 Deletar as view admin/classes, admin/courses/, admin/video_providers
3. A migration: 20260901163746_create_educational_core.php precisamos criar um nova que deve remover todas as tabelas criadas e seus foreign-keys
4. A migration: 20260907162159_create_logs.php precisamos remover o trigger criado e usar a estrutura:
```sql
created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- nesse caso não vamos ter o updated_at, mas é um exemplo
```
5. faça uma lista das tabelas que ficaram e quais delas ainda usam o trigger que não seja igual do ponto 4.
6. Deleta os arquivos em tests/unit/usecases/Admin/* e tests/unit/MY_ModelTest.php
