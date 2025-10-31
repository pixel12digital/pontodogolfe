# Como Instalar e Ativar o Tema Astra

## Situação Atual

✅ **Personalização do login já está configurada** nos temas:
- `wp-content/themes/astra-child/functions.php` 
- `wp-content/themes/ponto/functions.php`

## Passo a Passo para Ativar o Astra

### ⚡ Opção 1: Automática (via mu-plugin) - RECOMENDADO

**Para corrigir o tema "royal-elementor-kit" quebrado:**

1. Acesse QUALQUER página do WordPress: `https://pontodogolfeoutlet.com.br/wp-admin`
2. O mu-plugin `fix-broken-theme.php` irá **AUTOMATICAMENTE**:
   - Detectar que o Astra não está instalado
   - Baixar e instalar o Astra
   - Ativar o tema Astra
   - Mostrar uma notificação de sucesso
3. Pronto! O login já está personalizado

**Funciona mesmo sem estar logado!**

⚠️ **Importante**: Após a ativação bem-sucedida, você pode deletar:
- `wp-content/mu-plugins/fix-broken-theme.php`
- `wp-content/mu-plugins/force-install-astra.php`
- `fix-themes-config.php`
- `install-astra-simple.php`

### Opção 2: Script Manual (Se a automática não funcionar)

Se a opção automática não funcionar:

1. Acesse: `https://pontodogolfeoutlet.com.br/install-astra-simple.php`
2. O script irá:
   - Instalar o Astra
   - Ativar automaticamente
   - Mostrar mensagens de sucesso
3. Delete o arquivo após usar

**Este script não precisa de autenticação!**

### Opção 3: Manual (via painel admin)

1. Acesse: `https://pontodogolfeoutlet.com.br/wp-admin/themes.php`
2. Se o Astra não estiver instalado:
   - Clique em "Adicionar novo"
   - Procure por "Astra"
   - Clique em "Instalar"
3. Ative o tema Astra clicando em "Ativar"
4. Se preferir usar o child theme:
   - Ative o tema "Astra Child (Ponto do Golfe)"
   - A personalização do login já está configurada!

## Verificando se Funcionou

1. Acesse a página de login: `https://pontodogolfeoutlet.com.br/wp-login.php`
2. Você deve ver:
   - ✅ Logo do Ponto do Golfe (grande e centralizado)
   - ✅ Botão "Iniciar sessão" em verde escuro (#1F5D3F)
   - ✅ Hover em verde claro (#719B57)
   - ✅ Links em verde escuro
   - ✅ Background com gradiente suave

## Cores Configuradas

As cores da marca já estão definidas e prontas para uso:

```php
PDG_COLOR_PRIMARY      = #1F5D3F  // Verde escuro (principal)
PDG_COLOR_SECONDARY    = #719B57  // Verde claro (secundário)  
PDG_COLOR_WHITE        = #FFFFFF  // Branco
```

## Limpeza Após Instalação

Após o Astra estar instalado e ativo, você pode deletar:

- ❌ `wp-content/mu-plugins/fix-broken-theme.php` (correção automática)
- ❌ `wp-content/mu-plugins/force-install-astra.php` (instalação automática)
- ❌ `wp-content/mu-plugins/auto-install-astra.php` (versão antiga)
- ❌ `wp-content/mu-plugins/force-theme-ponto.php` (tema temporário - já desabilitado)
- ❌ `fix-themes-config.php` (script manual)

## Próximos Passos

Com o Astra instalado, você pode:

1. Personalizar o header/footer
2. Configurar o WooCommerce
3. Adicionar mais estilos com as cores da marca
4. Usar os padrões de blocos já criados em `astra-child/patterns/`

## Suporte

Se tiver problemas, verifique:
- Arquivo `wp-content/debug.log` para erros
- Permissões de arquivos e diretórios
- Se o Astra apareceu em `Aparência > Temas`

