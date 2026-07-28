- O controller de migration precisa ter revisão no fluxo do middleware
to achando que pode vazar a execução dessas migrations.
- A tabela de migrations hoje só está salvando a last version, eu preciso que ele
salva todas as migrations uma após a outra e marcando quem foi a um última executada.
- 