# Ponto do Golfe - WordPress Site

Este é o repositório do site Ponto do Golfe desenvolvido em WordPress.

## 📋 Informações do Projeto

- **CMS**: WordPress
- **Framework**: Elementor + Elementor Pro
- **Loja**: WooCommerce
- **Cache**: LiteSpeed Cache
- **Multilíngue**: TranslatePress (PT-BR / EN-US)

## 🛠️ Tecnologias

- PHP 7.4+
- MariaDB 11.8.3+
- WordPress Core
- Elementor & Elementor Pro
- WooCommerce
- Royal Elementor Addons
- TranslatePress
- LiteSpeed Cache
- Hostinger AI Assistant

## 📁 Estrutura do Projeto

```
pontodogolfe/
├── public_html/          # Diretório principal do WordPress
│   ├── wp-admin/        # Painel administrativo
│   ├── wp-content/      # Temas, plugins e uploads
│   ├── wp-includes/     # Arquivos core do WordPress
│   └── wp-config.php    # Configuração (NÃO incluído no repo)
└── README.md
```

## 🚀 Instalação Local (XAMPP)

### Pré-requisitos
- XAMPP instalado
- PHP 7.4 ou superior
- MySQL/MariaDB
- Acesso ao banco de dados remoto da Hostinger

### Passos

1. **Clone o repositório**
```bash
git clone [url-do-repositorio]
cd pontodogolfe
```

2. **Configure o wp-config.php**
   - Copie o arquivo `wp-config-sample.php` (se existir) ou crie manualmente
   - Configure as credenciais do banco de dados remoto

3. **Inicie o XAMPP**
   - Inicie Apache e MySQL no painel de controle do XAMPP

4. **Configure o Virtual Host** (opcional mas recomendado)
   
   Edite `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
   
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/pontodogolfe/public_html"
    ServerName pontodogolfe.local
    <Directory "C:/xampp/htdocs/pontodogolfe/public_html">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

   Adicione ao arquivo `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1    pontodogolfe.local
```

5. **Acesse o site**
   - Via localhost: `http://localhost/pontodogolfe/public_html/`
   - Via virtual host: `http://pontodogolfe.local/`

## 🔧 Configuração do Banco de Dados

O projeto usa um banco de dados remoto na Hostinger:

- **Host**: auth-db812.hstgr.io
- **Tipo**: MariaDB 11.8.3
- **Prefixo das tabelas**: `wp_`

⚠️ **IMPORTANTE**: As credenciais do banco de dados NÃO devem ser commitadas no Git por questões de segurança.

## 📝 Notas Importantes

### Arquivos Não Versionados
- `wp-config.php` (contém credenciais sensíveis)
- `wp-content/uploads/` (arquivos de mídia enviados pelos usuários)
- `wp-content/cache/` (arquivos de cache)
- Logs e arquivos temporários

### Plugins Principais
- **Elementor & Elementor Pro**: Construtor de páginas visual
- **WooCommerce**: Sistema de e-commerce
- **Royal Elementor Addons**: Widgets extras para Elementor
- **TranslatePress**: Tradução multilíngue
- **LiteSpeed Cache**: Otimização de desempenho
- **Hostinger AI Assistant**: Ferramentas de IA da Hostinger

## 🌐 Deploy

Este site está hospedado na Hostinger e conectado a um banco de dados remoto.

### Backup
- Sempre faça backup antes de fazer alterações significativas
- Use ferramentas como UpdraftPlus ou backup manual via phpMyAdmin

### Atualização
```bash
git pull origin main
# As alterações serão aplicadas diretamente ao servidor
```

## 🔐 Segurança

- Mantenha WordPress, plugins e temas atualizados
- Use senhas fortes e únicas
- Não compartilhe credenciais públicamente
- Faça backups regulares
- Use HTTPS em produção

## 📞 Suporte

Para questões técnicas ou configuração:
- Consulte a documentação do WordPress: https://wordpress.org/documentation/
- Documentação do Elementor: https://elementor.com/help/
- Documentação do WooCommerce: https://woocommerce.com/documentation/

## 📄 Licença

Este projeto é proprietário. Todos os direitos reservados.

