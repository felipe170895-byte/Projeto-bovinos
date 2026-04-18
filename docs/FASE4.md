# Fase 4 — Consolidação de Produção

Esta fase adiciona hardening de segurança e operação:

## Segurança aplicada
- Token CSRF para formulários POST (renderizado no HTML e com fallback JS).
- Cabeçalhos de segurança HTTP (CSP, frame/options, nosniff, referrer-policy).
- Rate limiting de login por e-mail (janela de 15 min).
- Auditoria de ações (`audit_logs`).
- `session_regenerate_id(true)` após login.

## Operação
- Migrações SQL incrementais em `sql/migrations`.
- Runner de migrações: `php scripts/migrate.php`.
- Backup: `scripts/db_backup.sh`.
- Restore: `scripts/db_restore.sh <arquivo.dump>`.

## Ajuste aplicado
- `001_init.sql` contém somente estrutura base.
- `002_hardening.sql` contém apenas componentes de hardening (sem duplicação lógica de fases).
