# 🎯 Sistema de Benefícios Corporativos - Desagio tecnico

Sistema desenvolvido em Laravel para gerenciamento de benefícios corporativos, permitindo que colaboradores solicitem diversos tipos de benefícios como vale-alimentação, vale-combustível, convênios e cartões-presente.

## 🚀 Tecnologias

- **Laravel 10** - Framework PHP
- **JWT Authentication** - Autenticação via tokens
- **MySQL** - Banco de dados relacional
- **PHPUnit** - Testes automatizados

## 📋 Funcionalidades

### ✅ Implementadas
- ✅ Autenticação JWT
- ✅ CRUD de benefícios
- ✅ Solicitação de benefícios
- ✅ Aprovação simples e dupla
- ✅ Regras de negócio (limite mensal, valores)
- ✅ Testes automatizados
- ✅ Validações completas

### 🔄 Regras de Negócio
1. **Limite mensal**: 1 solicitação por tipo de benefício por mês
2. **Aprovação dupla**: Alguns benefícios requerem 2 aprovações
3. **Validação de valores**: Respeita limite máximo por benefício
4. **Controle de acesso**: Funcionários, aprovadores e admins
