# Cores da Marca - Ponto do Golfe

## Paleta de Cores Oficial

### Cores Principais

#### Verde Escuro (Primary)
- **Código HEX**: `#1F5D3F`
- **Cor PHP**: `PDG_COLOR_PRIMARY`
- **Uso**: Cor principal da marca, botões principais, links importantes, destaques

#### Verde Claro (Secondary)
- **Código HEX**: `#719B57`
- **Cor PHP**: `PDG_COLOR_SECONDARY`
- **Uso**: Hover states, elementos secundários, backgrounds suaves

#### Branco
- **Código HEX**: `#FFFFFF`
- **Cor PHP**: `PDG_COLOR_WHITE`
- **Uso**: Texto em fundos escuros, backgrounds, espaços vazios

## Como Usar

### Em PHP (functions.php)

```php
// Usar as constantes já definidas
$cor_principal = PDG_COLOR_PRIMARY;
$cor_secundaria = PDG_COLOR_SECONDARY;
$cor_branca = PDG_COLOR_WHITE;
```

### Em CSS

```css
/* Verde escuro */
.minha-classe {
    color: #1F5D3F;
    background-color: #1F5D3F;
}

/* Verde claro */
.minha-classe:hover {
    background-color: #719B57;
}

/* Branco */
.texto-branco {
    color: #FFFFFF;
}
```

### Variáveis CSS (Para uso futuro)

```css
:root {
    --pdg-color-primary: #1F5D3F;
    --pdg-color-secondary: #719B57;
    --pdg-color-white: #FFFFFF;
}

.elemento {
    background-color: var(--pdg-color-primary);
}
```

## Aplicações Atuais

### ✅ Login do WordPress
As cores já estão configuradas na página de login com:
- Logo personalizado
- Botões com cores da marca
- Links com hover personalizado
- Inputs com foco personalizado

### 🎨 Próximas Aplicações Sugeridas
- Header do site
- Footer
- Botões do WooCommerce
- Formulários de contato
- CTAs (Call to Actions)
- Elementos de navegação

## Manutenção

**IMPORTANTE**: As cores estão definidas em:
1. `functions.php` - Constantes PHP (linhas 42-44)
2. `style.css` - Documentação CSS (linhas 11-18)
3. `CORES-DA-MARCA.md` - Este arquivo de documentação

Se precisar alterar as cores, atualize **todos** esses locais para manter a consistência.

## Referências

- **Logo**: https://pontodogolfeoutlet.com.br/wp-content/uploads/2025/10/Captura-de-tela-2025-10-31-085004.png
- **Descrição do Logo**: Logo "PONTO DO GOLFE" com elementos temáticos de golfe e elementos botânicos

---

**Última atualização**: 31/10/2025

