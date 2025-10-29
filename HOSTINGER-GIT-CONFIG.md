# Configuração do Git na Hostinger - CORRIGIR

## Problema Identificado
O deploy está indo para o diretório errado. A URL mostra `public_html/public_html/`, indicando duplo diretório.

## Solução

### No Painel da Hostinger:

1. **Acesse:** Gerenciamento do Git no painel
2. **Remova a integração atual**
3. **Crie nova integração com:**

   **Repositório:**
   ```
   https://github.com/pixel12digital/pontodogolfe.git
   ```
   
   **Branch:**
   ```
   master
   ```
   
   **Diretório:**
   ```
   public_html
   ```
   
   OU se a opção "subdirectory" estiver disponível:
   
   **Subdirectory:**
   ```
   public_html
   ```

### Importante:
- Se a Hostinger pedir "Directory", deixe **VAZIO** se quiser deploy direto em `public_html`
- Ou use apenas `public_html` (sem duplicar)

## Estrutura Esperada no Servidor:

```
/
└── public_html/          ← O Git deve fazer deploy AQUI
    ├── index.php        ← Deve aparecer aqui
    ├── wp-config.php    ← Você criou aqui manualmente
    ├── wp-admin/
    ├── wp-content/
    └── ...
```

## Depois de Ajustar:

1. Faça um novo push para ativar o webhook
2. Verifique se os arquivos aparecem em `public_html/` (não em `public_html/public_html/`)
3. Tente acessar o site novamente

