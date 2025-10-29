# CORRIGIR CONFIGURAÇÃO NA HOSTINGER

## ⚠️ PROBLEMA ATUAL
O deploy está criando `public_html/public_html/` no servidor.

## ✅ SOLUÇÃO - Passo a Passo

### 1. Acesse a Configuração Git na Hostinger
- Vá para: **Websites** > Seu Site > **Gerenciamento do Git**

### 2. Edite a Integração do Git
- Clique em **Editar** ou **Configurar**
- Procure o campo **Diretório** ou **Deploy Directory**

### 3. AJUSTE O CAMPO DIRETÓRIO
Atualmente deve estar assim:
```
❌ Diretório: public_html
```

Mude para:
```
✅ Diretório: (deixe VAZIO)
```

OU se tiver opção de subdiretório:
```
✅ Subdiretório: (deixe VAZIO)
```

### 4. Salve as Alterações
- Clique em **Salvar** ou **Atualizar**

### 5. DELETE A ESTRUTURA ERRADA NO SERVIDOR

Via File Manager da Hostinger:

**a) Delete a pasta `.git` dentro de `public_html/`**
- É uma pasta escondida, mas está visível
- Pode causar problemas de segurança

**b) MOVA os arquivos corretos**
- Entre em `public_html/public_html/`
- Selecione TODOS os arquivos e pastas
- MOVA para `public_html/`
- DELETE a pasta `public_html/public_html/` vazia

### 6. AGUARDE O PRÓXIMO DEPLOY
- Faça um pequeno push para ativar o webhook
- Aguarde alguns segundos
- Verifique se os arquivos agora estão em `public_html/` (não em `public_html/public_html/`)

## 📝 Estrutura Esperada (Depois de Corrigir)

```
/public_html/
├── index.php          ✅
├── wp-admin/          ✅
├── wp-content/        ✅
├── wp-includes/       ✅
├── .htaccess          ✅
├── wp-config.php      ✅ (que você criou manualmente)
└── ...outros arquivos
```

NÃO deve ter:
- ❌ `public_html/public_html/`
- ❌ `.git/` dentro de `public_html/`

## ⚡ COMANDO RÁPIDO - Fazer Push de Teste

Depois de corrigir, faça um push de teste:
```bash
# Vou fazer isso agora para você testar
```

