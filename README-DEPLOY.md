# Configuração de Deploy Automático - Hostinger

## Passos para Configurar o Webhook no GitHub

### 1. Acesse o Repositório no GitHub
Acesse: https://github.com/pixel12digital/pontodogolfe

### 2. Vá para Configurações de Webhooks
Acesse: https://github.com/pixel12digital/pontodogolfe/settings/hooks/new

### 3. Preencha as Informações do Webhook

- **Payload URL:** 
  ```
  https://webhooks.hostinger.com/deploy/b3233f43b58bcced65f93eaf651e34e5
  ```

- **Content type:** `application/json`

- **Events:** Selecione "Just the push event"

- **Active:** Deixe marcado ✅

### 4. Clique em "Add webhook"

---

## Como Funciona

1. Você faz alterações no código localmente
2. Faz commit e push para o GitHub
3. O GitHub envia um webhook para a Hostinger
4. A Hostinger automaticamente faz o deploy do código
5. Seu site é atualizado automaticamente! 🚀

---

## Configuração Inicial do Servidor

⚠️ **IMPORTANTE:** Depois do primeiro deploy, você precisa configurar o `wp-config.php` no servidor.

Arquivo: `public_html/wp-config.php`

```php
<?php
// Configurações do Banco de Dados
define( 'DB_NAME', 'u426126796_pontodogolfe' );
define( 'DB_USER', 'u426126796_pontodogolfe' );
define( 'DB_PASSWORD', 'Los@ngo#081081' );
define( 'DB_HOST', 'auth-db1075.hstgr.io' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// ... resto das configurações do seu wp-config.php atual
```

---

## Comandos Git para Deploy Manual

Se preferir fazer deploy manual:

```bash
# Fazer commit das alterações
git add .
git commit -m "Descrição das alterações"

# Enviar para o GitHub
git push origin master

# O webhook automaticamente fará o deploy na Hostinger
```

---

## Solução de Problemas

### Se o deploy automático não funcionar:
1. Verifique se o webhook está configurado corretamente no GitHub
2. Verifique se a URL do webhook está correta
3. Teste fazendo um push para ver se o webhook é ativado
4. Verifique os logs do webhook no GitHub (Settings > Webhooks > Seu Webhook > Recent Deliveries)

