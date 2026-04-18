# ReproBov

Base do sistema de reprodução bovina em PHP procedural + PostgreSQL.

## Configuração rápida
1. Crie banco PostgreSQL `reprobov`.
2. Execute `sql/schema.sql`.
3. Configure variáveis de ambiente `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.
4. Sirva a pasta `public/` com PHP embutido ou Apache/Nginx.

## Módulos implementados
- Autenticação e cadastro de usuários.
- Fazendas (CRUD inicial + troca de fazenda ativa).
- Administração básica (usuários, fazendas, vínculos).
- Animais (CRUD + detalhes).
- Lotes (CRUD + resumo).
- Eventos reprodutivos (CRUD inicial).
- Scanner de brinco por código de barras (modo leitor USB teclado).
