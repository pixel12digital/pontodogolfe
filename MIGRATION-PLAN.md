# Plano de Migração - Reorganizar Estrutura

## Problema Identificado
A Hostinger está fazendo deploy em `public_html/public_html/` porque:
- Nossos arquivos estão dentro de uma pasta `public_html/`
- A Hostinger já tem um `public_html/` no servidor
- Resultado: arquivos ficam em `public_html/public_html/`

## Solução

### Opção 1: Mudar configuração na Hostinger (MAIS RÁPIDA)
Na configuração do Git da Hostinger, mude o **Diretório** de:
- ❌ `public_html` 
- ✅ `public_html/public_html`

Ou deixe vazio se a opção existir.

### Opção 2: Reorganizar repositório (MELHOR A LONGO PRAZO)
Mover todos os arquivos de `public_html/` para a raiz do repositório.

**Comando local:**
```bash
# Mover conteúdo
cd c:\xampp\htdocs\pontodogolfe
move public_html\*.* .
move public_html\wp-admin .
move public_html\wp-content .
move public_html\wp-includes .

# Deletar pasta public_html vazia
rmdir public_html

# Atualizar .gitignore
# Commit e push
```

**Vantagens:**
- Estrutura mais limpa
- Deploy direto em `public_html` da Hostinger
- Sem duplicação de pastas

**Desvantagens:**
- Requer reconfigurar projeto local
- Pode quebrar desenvolvimento local temporariamente

## Recomendação
**TENTAR OPÇÃO 1 PRIMEIRO** - Mais rápida e não quebra nada.

## Status
Aguardando decisão do usuário.

