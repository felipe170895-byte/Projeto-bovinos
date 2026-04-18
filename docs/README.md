# ReproBov

Base do sistema de reprodução bovina em PHP procedural + PostgreSQL.

## Configuração rápida
1. Crie banco PostgreSQL `reprobov`.
2. Execute `php scripts/migrate.php` para aplicar schema/migrações.
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
- Bezerros (cadastro e listagem).
- Relatórios simples (resumos por tipo e mês).

## Fase 4 — hardening e operação
- CSRF e cabeçalhos de segurança.
- Auditoria de ações e rate limit de login.
- Migrações versionadas em `sql/migrations/`.
- Backup/restore com scripts em `scripts/`.

## Preparação para uso offline
- `manifest.webmanifest` para instalação PWA básica.
- `service-worker.js` para cache de recursos essenciais e fallback em `offline.html`.
- Registro automático do service worker via `assets/js/app.js`.
