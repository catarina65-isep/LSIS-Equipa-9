-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27-Jun-2025 às 09:54
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ficha_colaboradores`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `administrador`
--

CREATE TABLE `administrador` (
  `id_administrador` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `nivel_acesso` int(11) DEFAULT 100,
  `permissoes_especiais` text DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `alerta`
--

CREATE TABLE `alerta` (
  `id_alerta` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('Aviso','Alerta','Informação','Urgente') NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `id_colaborador` int(11) DEFAULT NULL,
  `id_equipa` int(11) DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_expiracao` datetime DEFAULT NULL,
  `prioridade` enum('Baixa','Média','Alta','Crítica') DEFAULT 'Média',
  `status` enum('Pendente','Em Andamento','Resolvido','Cancelado') DEFAULT 'Pendente',
  `id_responsavel` int(11) DEFAULT NULL,
  `data_resolucao` datetime DEFAULT NULL,
  `solucao` text DEFAULT NULL,
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL,
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `alerta_colaborador`
--

CREATE TABLE `alerta_colaborador` (
  `id_alerta_colaborador` int(11) NOT NULL,
  `id_alerta` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `api_acesso`
--

CREATE TABLE `api_acesso` (
  `id_api_acesso` int(11) NOT NULL,
  `chave_api` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `id_utilizador` int(11) NOT NULL,
  `data_expiracao` datetime DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cache`
--

CREATE TABLE `cache` (
  `chave` varchar(255) NOT NULL,
  `valor` longtext NOT NULL,
  `expiracao` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_utilizador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cache_sessao`
--

CREATE TABLE `cache_sessao` (
  `id_sessao` varchar(128) NOT NULL,
  `dados` longtext NOT NULL,
  `ultimo_acesso` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `campos_personalizados`
--

CREATE TABLE `campos_personalizados` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `rotulo` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `valor_padrao` varchar(255) DEFAULT NULL,
  `opcoes` text DEFAULT NULL,
  `categoria` varchar(50) NOT NULL,
  `obrigatorio` tinyint(1) NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `requer_comprovativo` tinyint(1) NOT NULL DEFAULT 0,
  `visivel_para` text DEFAULT NULL,
  `editavel_por` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `campos_personalizados_historico`
--

CREATE TABLE `campos_personalizados_historico` (
  `id` int(11) NOT NULL,
  `campo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `acao` varchar(50) NOT NULL,
  `dados_anteriores` text DEFAULT NULL,
  `dados_novos` text DEFAULT NULL,
  `data_acao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `campo_personalizado`
--

CREATE TABLE `campo_personalizado` (
  `id_campo` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `rotulo` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `categoria` varchar(50) DEFAULT 'outros',
  `opcoes` text DEFAULT NULL,
  `valor_padrao` text DEFAULT NULL,
  `tamanho_maximo` int(11) DEFAULT NULL,
  `obrigatorio` tinyint(1) DEFAULT 0,
  `visivel` tinyint(1) DEFAULT 1,
  `editavel` tinyint(1) DEFAULT 1,
  `validacao` text DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `secao` varchar(50) DEFAULT NULL,
  `grupo` varchar(50) DEFAULT NULL,
  `dica` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `campo_personalizado_categoria`
--

CREATE TABLE `campo_personalizado_categoria` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `cor` varchar(20) DEFAULT '#3498db',
  `ordem` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `campo_personalizado_categoria`
--

INSERT INTO `campo_personalizado_categoria` (`id_categoria`, `nome`, `descricao`, `icone`, `cor`, `ordem`, `ativo`, `data_criacao`, `data_atualizacao`) VALUES
(1, 'dados_pessoais', 'Dados Pessoais', 'user', '#3498db', 1, 1, '2025-06-26 15:39:16', '2025-06-26 15:39:16'),
(2, 'contato', 'Informações de Contato', 'phone', '#2ecc71', 2, 1, '2025-06-26 15:39:16', '2025-06-26 15:39:16'),
(3, 'documentos', 'Documentos', 'file-text', '#e74c3c', 3, 1, '2025-06-26 15:39:16', '2025-06-26 15:39:16'),
(4, 'endereco', 'Endereço', 'map-pin', '#9b59b6', 4, 1, '2025-06-26 15:39:16', '2025-06-26 15:39:16'),
(5, 'familiar', 'Familiar', 'users', '#e67e22', 5, 1, '2025-06-26 15:39:16', '2025-06-26 15:39:16'),
(6, 'academico', 'Acadêmico', 'graduation-cap', '#1abc9c', 6, 1, '2025-06-26 15:39:16', '2025-06-26 15:39:16'),
(7, 'profissional', 'Profissional', 'briefcase', '#f39c12', 7, 1, '2025-06-26 15:39:16', '2025-06-26 15:39:16'),
(8, 'beneficios', 'Benefícios', 'gift', '#e74c3c', 8, 1, '2025-06-26 15:39:16', '2025-06-26 15:39:16'),
(9, 'saude', 'Saúde', 'heart', '#e74c3c', 9, 1, '2025-06-26 15:39:16', '2025-06-26 15:39:16'),
(10, 'outros', 'Outros', 'more-horizontal', '#95a5a6', 99, 1, '2025-06-26 15:39:16', '2025-06-26 15:39:16');

-- --------------------------------------------------------

--
-- Estrutura da tabela `campo_personalizado_valor`
--

CREATE TABLE `campo_personalizado_valor` (
  `id_valor` int(11) NOT NULL,
  `id_campo` int(11) NOT NULL,
  `entidade_tipo` varchar(50) NOT NULL,
  `entidade_id` int(11) NOT NULL,
  `valor` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `colaborador`
--

CREATE TABLE `colaborador` (
  `id_colaborador` int(11) NOT NULL,
  `numero_mecanografico` varchar(20) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `nome_meio` varchar(50) DEFAULT NULL,
  `apelido` varchar(50) DEFAULT NULL,
  `genero` enum('Masculino','Feminino','Outro','Prefiro não dizer') DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `estado_civil` enum('Solteiro','Casado','União de Facto','Divorciado','Viúvo','Separado') DEFAULT NULL,
  `nacionalidade` varchar(50) DEFAULT 'Portuguesa',
  `naturalidade` varchar(100) DEFAULT NULL,
  `nif` varchar(9) DEFAULT NULL,
  `niss` varchar(11) DEFAULT NULL,
  `nib` varchar(25) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `telemovel` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `email_pessoal` varchar(100) DEFAULT NULL,
  `morada` text DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `localidade` varchar(100) DEFAULT NULL,
  `pais` varchar(50) DEFAULT 'Portugal',
  `foto` varchar(255) DEFAULT NULL,
  `id_funcao` int(11) DEFAULT NULL,
  `id_equipa` int(11) DEFAULT NULL,
  `id_gestor_direto` int(11) DEFAULT NULL,
  `tipo_contrato` enum('Sem Termo','Termo Certo','Prestação Serviço','Estágio Profissional','Outro') DEFAULT NULL,
  `data_entrada` date DEFAULT NULL,
  `data_saida` date DEFAULT NULL,
  `motivo_saida` text DEFAULT NULL,
  `periodo_experiencia_ate` date DEFAULT NULL,
  `remuneracao_bruta` decimal(10,2) DEFAULT NULL,
  `subsidio_alimentacao` decimal(10,2) DEFAULT 0.00,
  `subsidio_transporte` decimal(10,2) DEFAULT 0.00,
  `outros_beneficios` text DEFAULT NULL,
  `horario_trabalho` varchar(50) DEFAULT '09:00-18:00',
  `dias_ferias_ano` int(11) DEFAULT 22,
  `dias_ferias_gozados` int(11) DEFAULT 0,
  `ultimo_voucher_telemovel` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `estado` enum('Ativo','Inativo','Licença','Férias','Baixa Médica','Suspenso') DEFAULT 'Ativo',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `colaborador`
--

INSERT INTO `colaborador` (`id_colaborador`, `numero_mecanografico`, `nome`, `nome_meio`, `apelido`, `genero`, `data_nascimento`, `estado_civil`, `nacionalidade`, `naturalidade`, `nif`, `niss`, `nib`, `telefone`, `telemovel`, `email`, `email_pessoal`, `morada`, `codigo_postal`, `localidade`, `pais`, `foto`, `id_funcao`, `id_equipa`, `id_gestor_direto`, `tipo_contrato`, `data_entrada`, `data_saida`, `motivo_saida`, `periodo_experiencia_ate`, `remuneracao_bruta`, `subsidio_alimentacao`, `subsidio_transporte`, `outros_beneficios`, `horario_trabalho`, `dias_ferias_ano`, `dias_ferias_gozados`, `ultimo_voucher_telemovel`, `observacoes`, `estado`, `data_criacao`, `data_atualizacao`) VALUES
(1, NULL, 'Administrador do Sistema', NULL, NULL, NULL, NULL, NULL, 'Portuguesa', NULL, NULL, NULL, NULL, NULL, NULL, 'admin@tlantic.pt', NULL, NULL, NULL, NULL, 'Portugal', NULL, NULL, NULL, NULL, NULL, '2025-06-25', NULL, NULL, NULL, NULL, 0.00, 0.00, NULL, '09:00-18:00', 22, 0, NULL, NULL, 'Ativo', '2025-06-25 14:43:08', '2025-06-25 14:43:08'),
(2, NULL, 'Recursos Humanos', NULL, NULL, NULL, NULL, NULL, 'Portuguesa', NULL, NULL, NULL, NULL, NULL, NULL, 'rh@tlantic.pt', NULL, NULL, NULL, NULL, 'Portugal', NULL, NULL, NULL, NULL, NULL, '2025-06-25', NULL, NULL, NULL, NULL, 0.00, 0.00, NULL, '09:00-18:00', 22, 0, NULL, NULL, 'Ativo', '2025-06-25 14:43:08', '2025-06-25 14:43:08'),
(3, NULL, 'Coordenador de Equipa', NULL, NULL, NULL, NULL, NULL, 'Portuguesa', NULL, NULL, NULL, NULL, NULL, NULL, 'coordenador@tlantic.pt', NULL, NULL, NULL, NULL, 'Portugal', NULL, NULL, NULL, NULL, NULL, '2025-06-25', NULL, NULL, NULL, NULL, 0.00, 0.00, NULL, '09:00-18:00', 22, 0, NULL, NULL, 'Ativo', '2025-06-25 14:43:08', '2025-06-25 14:43:08'),
(4, NULL, 'Colaborador Comum', NULL, NULL, NULL, NULL, NULL, 'Portuguesa', NULL, NULL, NULL, NULL, NULL, NULL, 'colaborador@tlantic.pt', NULL, NULL, NULL, NULL, 'Portugal', NULL, NULL, NULL, NULL, NULL, '2025-06-25', NULL, NULL, NULL, NULL, 0.00, 0.00, NULL, '09:00-18:00', 22, 0, NULL, NULL, 'Ativo', '2025-06-25 14:43:08', '2025-06-25 14:43:08');

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracao`
--

CREATE TABLE `configuracao` (
  `id_configuracao` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` varchar(50) DEFAULT 'text',
  `opcoes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`opcoes`)),
  `grupo` varchar(50) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `editavel` tinyint(1) DEFAULT 1,
  `visivel` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `configuracao`
--

INSERT INTO `configuracao` (`id_configuracao`, `chave`, `valor`, `descricao`, `tipo`, `opcoes`, `grupo`, `ordem`, `editavel`, `visivel`, `data_criacao`, `data_atualizacao`, `id_utilizador_criacao`, `id_utilizador_atualizacao`) VALUES
(1, 'empresa_nome', 'Tlantic', 'Nome da empresa', 'text', NULL, 'Geral', 1, 1, 1, '2025-06-25 08:09:01', '2025-06-25 08:09:01', NULL, NULL),
(2, 'empresa_morada', 'Porto, Portugal', 'Morada da empresa', 'text', NULL, 'Geral', 2, 1, 1, '2025-06-25 08:09:01', '2025-06-25 08:09:01', NULL, NULL),
(3, 'empresa_nif', '', 'NIF da empresa', 'text', NULL, 'Geral', 3, 1, 1, '2025-06-25 08:09:01', '2025-06-25 08:09:01', NULL, NULL),
(4, 'email_notificacoes', 'notificacoes@empresa.com', 'Email para envio de notificações', 'email', NULL, 'Email', 10, 1, 1, '2025-06-25 08:09:01', '2025-06-25 08:09:01', NULL, NULL),
(5, 'email_suporte', 'suporte@empresa.com', 'Email de suporte', 'email', NULL, 'Email', 11, 1, 1, '2025-06-25 08:09:01', '2025-06-25 08:09:01', NULL, NULL),
(6, 'dias_ferias_ano', '22', 'Número de dias de férias por ano', 'number', NULL, 'RH', 20, 1, 1, '2025-06-25 08:09:01', '2025-06-25 08:09:01', NULL, NULL),
(7, 'periodo_experiencia', '90', 'Período experimental em dias', 'number', NULL, 'RH', 21, 1, 1, '2025-06-25 08:09:01', '2025-06-25 08:09:01', NULL, NULL),
(8, 'alerta_atualizacao_dados', '365', 'Dias para alerta de atualização de dados', 'number', NULL, 'Alertas', 30, 1, 1, '2025-06-25 08:09:01', '2025-06-25 08:09:01', NULL, NULL),
(9, 'alerta_voucher_telemovel', '700', 'Dias para alerta de renovação de voucher de telemóvel', 'number', NULL, 'Alertas', 31, 1, 1, '2025-06-25 08:09:01', '2025-06-25 08:09:01', NULL, NULL),
(10, 'campo_personalizado_ativado', '1', 'Ativar sistema de campos personalizados', 'toggle', NULL, 'Campos Personalizados', 1, 1, 1, '2025-06-26 15:40:35', '2025-06-26 15:40:35', NULL, NULL),
(11, 'campo_personalizado_edicao_restrita', '1', 'Restringir edição de campos a administradores', 'toggle', NULL, 'Campos Personalizados', 2, 1, 1, '2025-06-26 15:40:35', '2025-06-26 15:40:35', NULL, NULL),
(12, 'campo_personalizado_historico_alteracoes', '1', 'Manter histórico de alterações dos campos', 'toggle', NULL, 'Campos Personalizados', 3, 1, 1, '2025-06-26 15:40:35', '2025-06-26 15:40:35', NULL, NULL),
(13, 'campo_personalizado_max_tamanho', '255', 'Tamanho máximo padrão para campos de texto', 'number', NULL, 'Campos Personalizados', 4, 1, 1, '2025-06-26 15:40:35', '2025-06-26 15:40:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `contato_emergencia`
--

CREATE TABLE `contato_emergencia` (
  `id_contato` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `parentesco` varchar(50) DEFAULT NULL,
  `telefone_principal` varchar(20) NOT NULL,
  `telefone_alternativo` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `morada` text DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `pais` varchar(50) DEFAULT 'Portugal',
  `observacoes` text DEFAULT NULL,
  `contato_principal` tinyint(1) DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `convidado`
--

CREATE TABLE `convidado` (
  `id_convidado` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `validade_convite` date DEFAULT NULL,
  `responsavel` int(11) DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `coordenador`
--

CREATE TABLE `coordenador` (
  `id_coordenador` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `id_equipa` int(11) DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT 'Coordenador',
  `tipo_coordenacao` enum('Equipa','Departamento','Geral') NOT NULL,
  `permissoes_especificas` text DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `observacoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Acionadores `coordenador`
--
DELIMITER $$
CREATE TRIGGER `before_update_coordenador` BEFORE UPDATE ON `coordenador` FOR EACH ROW BEGIN
    SET NEW.data_atualizacao = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `dashboard`
--

CREATE TABLE `dashboard` (
  `id_dashboard` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `layout` text NOT NULL,
  `visibilidade` enum('privado','publico','por_perfil') DEFAULT 'privado',
  `ativo` tinyint(1) DEFAULT 1,
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `dashboard_widget`
--

CREATE TABLE `dashboard_widget` (
  `id_widget` int(11) NOT NULL,
  `id_dashboard` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `posicao` int(11) NOT NULL,
  `configuracao` text NOT NULL,
  `largura` int(11) DEFAULT 4,
  `altura` int(11) DEFAULT 4,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `departamento`
--

CREATE TABLE `departamento` (
  `id_departamento` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `id_gestor` int(11) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `documento`
--

CREATE TABLE `documento` (
  `id_documento` int(11) NOT NULL,
  `id_colaborador` int(11) DEFAULT NULL,
  `tipo_documento` varchar(50) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `caminho_arquivo` varchar(255) NOT NULL,
  `tamanho` int(11) DEFAULT NULL,
  `formato` varchar(20) DEFAULT NULL,
  `data_validade` date DEFAULT NULL,
  `obrigatorio` tinyint(1) DEFAULT 0,
  `status` enum('Pendente','Aprovado','Rejeitado','Expirado') DEFAULT 'Pendente',
  `motivo_rejeicao` text DEFAULT NULL,
  `id_aprovador` int(11) DEFAULT NULL,
  `data_aprovacao` datetime DEFAULT NULL,
  `data_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilizador_upload` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `equipa`
--

CREATE TABLE `equipa` (
  `id_equipa` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `id_equipa_pai` int(11) DEFAULT NULL,
  `nivel` int(11) DEFAULT 1,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_coordenador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `favorito`
--

CREATE TABLE `favorito` (
  `id_favorito` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ferias`
--

CREATE TABLE `ferias` (
  `id_ferias` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `dias_uteis` int(11) NOT NULL,
  `tipo` enum('Férias','Licença','Outro') DEFAULT 'Férias',
  `estado` enum('Pendente','Aprovado','Rejeitado','Cancelado') DEFAULT 'Pendente',
  `motivo_rejeicao` text DEFAULT NULL,
  `id_aprovador` int(11) DEFAULT NULL,
  `data_aprovacao` datetime DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fila_email`
--

CREATE TABLE `fila_email` (
  `id_email` int(11) NOT NULL,
  `destinatario` varchar(255) NOT NULL,
  `assunto` varchar(255) NOT NULL,
  `mensagem` longtext NOT NULL,
  `headers` text DEFAULT NULL,
  `anexos` text DEFAULT NULL,
  `prioridade` int(11) DEFAULT 3,
  `tentativas` int(11) DEFAULT 0,
  `ultima_tentativa` timestamp NULL DEFAULT NULL,
  `status` enum('pendente','enviando','enviado','erro') DEFAULT 'pendente',
  `mensagem_erro` text DEFAULT NULL,
  `data_agendada` timestamp NULL DEFAULT NULL,
  `data_envio` timestamp NULL DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fila_impressao`
--

CREATE TABLE `fila_impressao` (
  `id_impressao` int(11) NOT NULL,
  `documento_nome` varchar(255) NOT NULL,
  `documento_tipo` varchar(50) NOT NULL,
  `conteudo` longtext NOT NULL,
  `impressora` varchar(100) DEFAULT NULL,
  `copias` int(11) DEFAULT 1,
  `opcoes` text DEFAULT NULL,
  `status` enum('pendente','processando','impresso','erro') DEFAULT 'pendente',
  `mensagem_erro` text DEFAULT NULL,
  `id_utilizador` int(11) DEFAULT NULL,
  `data_agendada` timestamp NULL DEFAULT NULL,
  `data_impressao` timestamp NULL DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `filtro_salvo`
--

CREATE TABLE `filtro_salvo` (
  `id_filtro` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `filtros` text NOT NULL,
  `partilhado` tinyint(1) DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcao`
--

CREATE TABLE `funcao` (
  `id_funcao` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `nivel_hierarquico` int(11) DEFAULT 1,
  `id_departamento` int(11) DEFAULT NULL,
  `salario_min` decimal(10,2) DEFAULT NULL,
  `salario_max` decimal(10,2) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `habilitacao`
--

CREATE TABLE `habilitacao` (
  `id_habilitacao` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `nivel` varchar(100) NOT NULL,
  `curso` varchar(255) NOT NULL,
  `instituicao` varchar(255) NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `concluido` tinyint(1) DEFAULT 0,
  `media_final` decimal(5,2) DEFAULT NULL,
  `pais` varchar(50) DEFAULT 'Portugal',
  `cidade` varchar(100) DEFAULT NULL,
  `anexo_certificado` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico_acesso`
--

CREATE TABLE `historico_acesso` (
  `id_historico` int(11) NOT NULL,
  `id_utilizador` int(11) DEFAULT NULL,
  `acao` varchar(100) NOT NULL,
  `modulo` varchar(50) DEFAULT NULL,
  `id_registro` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `dados` text DEFAULT NULL,
  `data_acesso` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico_alteracoes`
--

CREATE TABLE `historico_alteracoes` (
  `id_alteracao` int(11) NOT NULL,
  `tabela_afetada` varchar(50) NOT NULL,
  `id_registro` int(11) NOT NULL,
  `campo_alterado` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `novo_valor` text DEFAULT NULL,
  `tipo_operacao` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `id_utilizador` int(11) DEFAULT NULL,
  `data_alteracao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_origem` varchar(45) DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico_relatorio`
--

CREATE TABLE `historico_relatorio` (
  `id_historico` int(11) NOT NULL,
  `id_relatorio` int(11) DEFAULT NULL,
  `nome_relatorio` varchar(100) NOT NULL,
  `parametros_utilizados` text DEFAULT NULL,
  `formato_gerado` varchar(10) NOT NULL,
  `caminho_arquivo` varchar(255) DEFAULT NULL,
  `tamanho_arquivo` int(11) DEFAULT NULL,
  `id_utilizador` int(11) DEFAULT NULL,
  `data_geracao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico_tarefa`
--

CREATE TABLE `historico_tarefa` (
  `id_historico` int(11) NOT NULL,
  `id_tarefa` varchar(128) NOT NULL,
  `inicio_execucao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fim_execucao` timestamp NULL DEFAULT NULL,
  `status` enum('sucesso','erro','cancelado') NOT NULL,
  `mensagem` text DEFAULT NULL,
  `dados_execucao` text DEFAULT NULL,
  `id_utilizador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `log_sistema`
--

CREATE TABLE `log_sistema` (
  `id_log` int(11) NOT NULL,
  `nivel` enum('info','aviso','erro','critico') NOT NULL,
  `origem` varchar(100) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `dados` text DEFAULT NULL,
  `ip_origem` varchar(45) DEFAULT NULL,
  `id_utilizador` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `metadado`
--

CREATE TABLE `metadado` (
  `id_metadado` int(11) NOT NULL,
  `entidade_tipo` varchar(50) NOT NULL,
  `entidade_id` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `modelo_documento`
--

CREATE TABLE `modelo_documento` (
  `id_modelo` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `conteudo` longtext NOT NULL,
  `tipo_documento` varchar(50) NOT NULL,
  `extensao` varchar(10) DEFAULT 'docx',
  `tamanho` int(11) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `obrigatorio` tinyint(1) DEFAULT 0,
  `id_departamento` int(11) DEFAULT NULL,
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `modelo_email`
--

CREATE TABLE `modelo_email` (
  `id_modelo_email` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `assunto` varchar(255) NOT NULL,
  `conteudo` longtext NOT NULL,
  `variaveis` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacao`
--

CREATE TABLE `notificacao` (
  `id_notificacao` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `mensagem` text DEFAULT NULL,
  `tipo` enum('Sistema','Alerta','Mensagem','Tarefa','Aprovação','Outro') NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_leitura` datetime DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `id_referencia` int(11) DEFAULT NULL,
  `tabela_referencia` varchar(50) DEFAULT NULL,
  `prioridade` enum('Baixa','Média','Alta','Urgente') DEFAULT 'Média',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_expiracao` datetime DEFAULT NULL,
  `id_utilizador_criacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `perfilacesso`
--

CREATE TABLE `perfilacesso` (
  `id_perfil_acesso` int(11) NOT NULL,
  `descricao` varchar(50) NOT NULL,
  `nivel_acesso` int(11) NOT NULL DEFAULT 0,
  `permissoes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissoes`)),
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `perfilacesso`
--

INSERT INTO `perfilacesso` (`id_perfil_acesso`, `descricao`, `nivel_acesso`, `permissoes`, `ativo`, `data_criacao`) VALUES
(1, 'Administrador', 100, '{\"todos\": true, \"campos_personalizados\": {\"visualizar\": true, \"criar\": true, \"editar\": true, \"excluir\": true, \"categorias\": {\"visualizar\": true, \"criar\": true, \"editar\": true, \"excluir\": true}}}', 1, '2025-06-25 08:09:00'),
(2, 'RH', 80, '{\"colaboradores\": {\"ler\": true, \"editar\": true, \"criar\": true, \"excluir\": false}, \"relatorios\": true, \"documentos\": true, \"campos_personalizados\": {\"visualizar\": true, \"criar\": true, \"editar\": true, \"excluir\": false, \"categorias\": {\"visualizar\": true, \"criar\": false, \"editar\": false, \"excluir\": false}}}', 1, '2025-06-25 08:09:00'),
(3, 'Coordenador', 60, '{\"equipa\": true, \"colaboradores\": {\"ler\": true, \"editar_equipa\": true}, \"relatorios\": true, \"campos_personalizados\": {\"visualizar\": true, \"criar\": false, \"editar\": false, \"excluir\": false, \"categorias\": {\"visualizar\": true, \"criar\": false, \"editar\": false, \"excluir\": false}}}', 1, '2025-06-25 08:09:00'),
(4, 'Colaborador', 40, '{\"perfil\": true, \"documentos\": true}', 1, '2025-06-25 08:09:00'),
(5, 'Convidado', 20, '{\"perfil\": true}', 1, '2025-06-25 08:09:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `permissoes`
--

CREATE TABLE `permissoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `grupo` varchar(100) DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `permissoes`
--

INSERT INTO `permissoes` (`id`, `nome`, `descricao`, `grupo`, `data_criacao`) VALUES
(1, 'campos_visualizar', 'Visualizar campos personalizados', 'Campos Personalizados', '2025-06-26 15:33:51'),
(2, 'campos_criar', 'Criar campos personalizados', 'Campos Personalizados', '2025-06-26 15:33:51'),
(3, 'campos_editar', 'Editar campos personalizados', 'Campos Personalizados', '2025-06-26 15:33:51'),
(4, 'campos_excluir', 'Excluir campos personalizados', 'Campos Personalizados', '2025-06-26 15:33:51');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pesquisa_salva`
--

CREATE TABLE `pesquisa_salva` (
  `id_pesquisa` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `parametros` text NOT NULL,
  `partilhada` tinyint(1) DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `recursos_humanos`
--

CREATE TABLE `recursos_humanos` (
  `id_rh` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `departamento` varchar(100) DEFAULT 'Recursos Humanos',
  `cargo` varchar(100) DEFAULT 'Técnico de RH',
  `permissoes_rh` text DEFAULT NULL,
  `acesso_total` tinyint(1) DEFAULT 0,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `relacionamento`
--

CREATE TABLE `relacionamento` (
  `id_relacionamento` int(11) NOT NULL,
  `origem_tipo` varchar(50) NOT NULL,
  `origem_id` int(11) NOT NULL,
  `destino_tipo` varchar(50) NOT NULL,
  `destino_id` int(11) NOT NULL,
  `tipo_relacionamento` varchar(50) NOT NULL,
  `dados` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `relatorio_personalizado`
--

CREATE TABLE `relatorio_personalizado` (
  `id_relatorio` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `consulta_sql` text NOT NULL,
  `parametros` text DEFAULT NULL,
  `formato_saida` enum('pdf','xlsx','csv','html') DEFAULT 'pdf',
  `agendamento` varchar(100) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `sessao`
--

CREATE TABLE `sessao` (
  `id_sessao` varchar(128) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `dados` text DEFAULT NULL,
  `ultima_atividade` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data_expiracao` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tarefa_agendada`
--

CREATE TABLE `tarefa_agendada` (
  `id_tarefa` varchar(128) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `frequencia` varchar(50) NOT NULL,
  `proxima_execucao` timestamp NULL DEFAULT NULL,
  `ultima_execucao` timestamp NULL DEFAULT NULL,
  `status_ultima_execucao` enum('sucesso','erro','executando') DEFAULT NULL,
  `mensagem_erro` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `configuracoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `tarefa_agendada`
--

INSERT INTO `tarefa_agendada` (`id_tarefa`, `nome`, `tipo`, `frequencia`, `proxima_execucao`, `ultima_execucao`, `status_ultima_execucao`, `mensagem_erro`, `ativo`, `configuracoes`, `data_criacao`, `data_atualizacao`) VALUES
('atualizar_estatisticas', 'Atualizar Estatísticas', 'relatorio', '0 1 * * *', NULL, NULL, NULL, NULL, 1, '{}', '2025-06-25 08:09:01', '2025-06-25 08:09:01'),
('backup_bd', 'Backup do Banco de Dados', 'backup', '0 2 * * *', NULL, NULL, NULL, NULL, 1, '{}', '2025-06-25 08:09:01', '2025-06-25 08:09:01'),
('enviar_emails_pendentes', 'Enviar Emails Pendentes', 'email', '*/5 * * * *', NULL, NULL, NULL, NULL, 1, '{}', '2025-06-25 08:09:01', '2025-06-25 08:09:01'),
('limpeza_logs', 'Limpeza de Logs Antigos', 'sistema', '0 0 * * 0', NULL, NULL, NULL, NULL, 1, '{\"dias_manter\": 90}', '2025-06-25 08:09:01', '2025-06-25 08:09:01'),
('verificar_vencimentos', 'Verificar Vencimentos', 'notificacao', '0 9 * * 1', NULL, NULL, NULL, NULL, 1, '{}', '2025-06-25 08:09:01', '2025-06-25 08:09:01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `traducao`
--

CREATE TABLE `traducao` (
  `id_traducao` int(11) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `texto_pt` text DEFAULT NULL,
  `texto_en` text DEFAULT NULL,
  `texto_es` text DEFAULT NULL,
  `texto_fr` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizador`
--

CREATE TABLE `utilizador` (
  `id_utilizador` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `id_colaborador` int(11) DEFAULT NULL,
  `id_perfil_acesso` int(11) NOT NULL,
  `ultimo_login` datetime DEFAULT NULL,
  `ip_ultimo_login` varchar(45) DEFAULT NULL,
  `token_recuperacao` varchar(100) DEFAULT NULL,
  `token_expiracao` datetime DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `bloqueado` tinyint(1) DEFAULT 0,
  `motivo_bloqueio` text DEFAULT NULL,
  `data_bloqueio` datetime DEFAULT NULL,
  `tentativas_login` int(11) DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `utilizador`
--

INSERT INTO `utilizador` (`id_utilizador`, `username`, `email`, `password_hash`, `id_colaborador`, `id_perfil_acesso`, `ultimo_login`, `ip_ultimo_login`, `token_recuperacao`, `token_expiracao`, `ativo`, `bloqueado`, `motivo_bloqueio`, `data_bloqueio`, `tentativas_login`, `data_criacao`, `data_atualizacao`) VALUES
(1, 'admin', 'admin@tlantic.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, '2025-06-25 14:37:09', '2025-06-25 15:00:53'),
(2, 'rh', 'rh@tlantic.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 2, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, '2025-06-25 14:37:09', '2025-06-25 15:00:53'),
(3, 'coordenador', 'coordenador@tlantic.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 3, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, '2025-06-25 14:37:09', '2025-06-25 15:00:53'),
(4, 'colaborador', 'colaborador@tlantic.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 4, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, '2025-06-25 14:37:09', '2025-06-25 15:00:53');

-- --------------------------------------------------------

--
-- Estrutura da tabela `viatura`
--

CREATE TABLE `viatura` (
  `id_viatura` int(11) NOT NULL,
  `id_colaborador` int(11) DEFAULT NULL,
  `matricula` varchar(20) DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `cor` varchar(30) DEFAULT NULL,
  `ano_fabrico` int(11) DEFAULT NULL,
  `capacidade_passageiros` int(11) DEFAULT NULL,
  `data_inspecao` date DEFAULT NULL,
  `proxima_inspecao` date DEFAULT NULL,
  `seguro_validade` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilizador_criacao` int(11) DEFAULT NULL,
  `id_utilizador_atualizacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_administrador`),
  ADD UNIQUE KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `alerta`
--
ALTER TABLE `alerta`
  ADD PRIMARY KEY (`id_alerta`),
  ADD KEY `id_colaborador` (`id_colaborador`),
  ADD KEY `id_equipa` (`id_equipa`),
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `id_responsavel` (`id_responsavel`),
  ADD KEY `fk_alerta_utilizador` (`id_utilizador_criacao`),
  ADD KEY `fk_alerta_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `alerta_colaborador`
--
ALTER TABLE `alerta_colaborador`
  ADD PRIMARY KEY (`id_alerta_colaborador`),
  ADD KEY `id_alerta` (`id_alerta`),
  ADD KEY `id_colaborador` (`id_colaborador`);

--
-- Índices para tabela `api_acesso`
--
ALTER TABLE `api_acesso`
  ADD PRIMARY KEY (`id_api_acesso`),
  ADD UNIQUE KEY `chave_api` (`chave_api`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`chave`),
  ADD KEY `idx_cache_utilizador` (`id_utilizador`);

--
-- Índices para tabela `cache_sessao`
--
ALTER TABLE `cache_sessao`
  ADD PRIMARY KEY (`id_sessao`);

--
-- Índices para tabela `campos_personalizados`
--
ALTER TABLE `campos_personalizados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices para tabela `campos_personalizados_historico`
--
ALTER TABLE `campos_personalizados_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campo_id` (`campo_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `campo_personalizado`
--
ALTER TABLE `campo_personalizado`
  ADD PRIMARY KEY (`id_campo`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD KEY `categoria` (`categoria`),
  ADD KEY `ativo` (`ativo`);

--
-- Índices para tabela `campo_personalizado_categoria`
--
ALTER TABLE `campo_personalizado_categoria`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices para tabela `campo_personalizado_valor`
--
ALTER TABLE `campo_personalizado_valor`
  ADD PRIMARY KEY (`id_valor`),
  ADD UNIQUE KEY `campo_entidade` (`id_campo`,`entidade_tipo`,`entidade_id`),
  ADD KEY `entidade` (`entidade_tipo`,`entidade_id`),
  ADD KEY `id_campo` (`id_campo`);

--
-- Índices para tabela `colaborador`
--
ALTER TABLE `colaborador`
  ADD PRIMARY KEY (`id_colaborador`),
  ADD UNIQUE KEY `numero_mecanografico` (`numero_mecanografico`),
  ADD UNIQUE KEY `nif` (`nif`),
  ADD UNIQUE KEY `niss` (`niss`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_funcao` (`id_funcao`),
  ADD KEY `id_equipa` (`id_equipa`),
  ADD KEY `id_gestor_direto` (`id_gestor_direto`);

--
-- Índices para tabela `configuracao`
--
ALTER TABLE `configuracao`
  ADD PRIMARY KEY (`id_configuracao`),
  ADD UNIQUE KEY `chave` (`chave`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `id_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `contato_emergencia`
--
ALTER TABLE `contato_emergencia`
  ADD PRIMARY KEY (`id_contato`),
  ADD KEY `id_colaborador` (`id_colaborador`);

--
-- Índices para tabela `convidado`
--
ALTER TABLE `convidado`
  ADD PRIMARY KEY (`id_convidado`),
  ADD UNIQUE KEY `id_utilizador` (`id_utilizador`),
  ADD KEY `responsavel` (`responsavel`);

--
-- Índices para tabela `coordenador`
--
ALTER TABLE `coordenador`
  ADD PRIMARY KEY (`id_coordenador`),
  ADD UNIQUE KEY `id_utilizador` (`id_utilizador`),
  ADD KEY `id_equipa` (`id_equipa`),
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `id_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `dashboard`
--
ALTER TABLE `dashboard`
  ADD PRIMARY KEY (`id_dashboard`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `id_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `dashboard_widget`
--
ALTER TABLE `dashboard_widget`
  ADD PRIMARY KEY (`id_widget`),
  ADD KEY `id_dashboard` (`id_dashboard`);

--
-- Índices para tabela `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id_departamento`);

--
-- Índices para tabela `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `id_colaborador` (`id_colaborador`),
  ADD KEY `id_aprovador` (`id_aprovador`),
  ADD KEY `id_utilizador_upload` (`id_utilizador_upload`);

--
-- Índices para tabela `equipa`
--
ALTER TABLE `equipa`
  ADD PRIMARY KEY (`id_equipa`),
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `id_equipa_pai` (`id_equipa_pai`),
  ADD KEY `id_coordenador` (`id_coordenador`);

--
-- Índices para tabela `favorito`
--
ALTER TABLE `favorito`
  ADD PRIMARY KEY (`id_favorito`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `ferias`
--
ALTER TABLE `ferias`
  ADD PRIMARY KEY (`id_ferias`),
  ADD KEY `id_colaborador` (`id_colaborador`),
  ADD KEY `id_aprovador` (`id_aprovador`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `id_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `fila_email`
--
ALTER TABLE `fila_email`
  ADD PRIMARY KEY (`id_email`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`);

--
-- Índices para tabela `fila_impressao`
--
ALTER TABLE `fila_impressao`
  ADD PRIMARY KEY (`id_impressao`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `filtro_salvo`
--
ALTER TABLE `filtro_salvo`
  ADD PRIMARY KEY (`id_filtro`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `funcao`
--
ALTER TABLE `funcao`
  ADD PRIMARY KEY (`id_funcao`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `id_departamento` (`id_departamento`);

--
-- Índices para tabela `habilitacao`
--
ALTER TABLE `habilitacao`
  ADD PRIMARY KEY (`id_habilitacao`),
  ADD KEY `id_colaborador` (`id_colaborador`),
  ADD KEY `anexo_certificado` (`anexo_certificado`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `id_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `historico_acesso`
--
ALTER TABLE `historico_acesso`
  ADD PRIMARY KEY (`id_historico`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `historico_alteracoes`
--
ALTER TABLE `historico_alteracoes`
  ADD PRIMARY KEY (`id_alteracao`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `historico_relatorio`
--
ALTER TABLE `historico_relatorio`
  ADD PRIMARY KEY (`id_historico`),
  ADD KEY `id_relatorio` (`id_relatorio`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `historico_tarefa`
--
ALTER TABLE `historico_tarefa`
  ADD PRIMARY KEY (`id_historico`),
  ADD KEY `id_tarefa` (`id_tarefa`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `log_sistema`
--
ALTER TABLE `log_sistema`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `metadado`
--
ALTER TABLE `metadado`
  ADD PRIMARY KEY (`id_metadado`),
  ADD UNIQUE KEY `entidade_chave` (`entidade_tipo`,`entidade_id`,`chave`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `id_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `modelo_documento`
--
ALTER TABLE `modelo_documento`
  ADD PRIMARY KEY (`id_modelo`),
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `id_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `modelo_email`
--
ALTER TABLE `modelo_email`
  ADD PRIMARY KEY (`id_modelo_email`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `id_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `notificacao`
--
ALTER TABLE `notificacao`
  ADD PRIMARY KEY (`id_notificacao`),
  ADD KEY `id_utilizador` (`id_utilizador`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`);

--
-- Índices para tabela `perfilacesso`
--
ALTER TABLE `perfilacesso`
  ADD PRIMARY KEY (`id_perfil_acesso`);

--
-- Índices para tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices para tabela `pesquisa_salva`
--
ALTER TABLE `pesquisa_salva`
  ADD PRIMARY KEY (`id_pesquisa`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `recursos_humanos`
--
ALTER TABLE `recursos_humanos`
  ADD PRIMARY KEY (`id_rh`),
  ADD UNIQUE KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `relacionamento`
--
ALTER TABLE `relacionamento`
  ADD PRIMARY KEY (`id_relacionamento`),
  ADD UNIQUE KEY `relacionamento_unico` (`origem_tipo`,`origem_id`,`destino_tipo`,`destino_id`,`tipo_relacionamento`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`);

--
-- Índices para tabela `relatorio_personalizado`
--
ALTER TABLE `relatorio_personalizado`
  ADD PRIMARY KEY (`id_relatorio`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `id_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `sessao`
--
ALTER TABLE `sessao`
  ADD PRIMARY KEY (`id_sessao`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `tarefa_agendada`
--
ALTER TABLE `tarefa_agendada`
  ADD PRIMARY KEY (`id_tarefa`);

--
-- Índices para tabela `traducao`
--
ALTER TABLE `traducao`
  ADD PRIMARY KEY (`id_traducao`),
  ADD UNIQUE KEY `chave_modulo` (`chave`,`modulo`),
  ADD KEY `id_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `id_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- Índices para tabela `utilizador`
--
ALTER TABLE `utilizador`
  ADD PRIMARY KEY (`id_utilizador`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `id_colaborador` (`id_colaborador`),
  ADD KEY `id_perfil_acesso` (`id_perfil_acesso`);

--
-- Índices para tabela `viatura`
--
ALTER TABLE `viatura`
  ADD PRIMARY KEY (`id_viatura`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD KEY `fk_viatura_colaborador` (`id_colaborador`),
  ADD KEY `fk_viatura_utilizador_criacao` (`id_utilizador_criacao`),
  ADD KEY `fk_viatura_utilizador_atualizacao` (`id_utilizador_atualizacao`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_administrador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `alerta`
--
ALTER TABLE `alerta`
  MODIFY `id_alerta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `alerta_colaborador`
--
ALTER TABLE `alerta_colaborador`
  MODIFY `id_alerta_colaborador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `api_acesso`
--
ALTER TABLE `api_acesso`
  MODIFY `id_api_acesso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `campos_personalizados`
--
ALTER TABLE `campos_personalizados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `campos_personalizados_historico`
--
ALTER TABLE `campos_personalizados_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `campo_personalizado`
--
ALTER TABLE `campo_personalizado`
  MODIFY `id_campo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `campo_personalizado_categoria`
--
ALTER TABLE `campo_personalizado_categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `campo_personalizado_valor`
--
ALTER TABLE `campo_personalizado_valor`
  MODIFY `id_valor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `colaborador`
--
ALTER TABLE `colaborador`
  MODIFY `id_colaborador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `configuracao`
--
ALTER TABLE `configuracao`
  MODIFY `id_configuracao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `contato_emergencia`
--
ALTER TABLE `contato_emergencia`
  MODIFY `id_contato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `convidado`
--
ALTER TABLE `convidado`
  MODIFY `id_convidado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `coordenador`
--
ALTER TABLE `coordenador`
  MODIFY `id_coordenador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dashboard`
--
ALTER TABLE `dashboard`
  MODIFY `id_dashboard` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dashboard_widget`
--
ALTER TABLE `dashboard_widget`
  MODIFY `id_widget` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documento`
--
ALTER TABLE `documento`
  MODIFY `id_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `equipa`
--
ALTER TABLE `equipa`
  MODIFY `id_equipa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `favorito`
--
ALTER TABLE `favorito`
  MODIFY `id_favorito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ferias`
--
ALTER TABLE `ferias`
  MODIFY `id_ferias` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fila_email`
--
ALTER TABLE `fila_email`
  MODIFY `id_email` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fila_impressao`
--
ALTER TABLE `fila_impressao`
  MODIFY `id_impressao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `filtro_salvo`
--
ALTER TABLE `filtro_salvo`
  MODIFY `id_filtro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `funcao`
--
ALTER TABLE `funcao`
  MODIFY `id_funcao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `habilitacao`
--
ALTER TABLE `habilitacao`
  MODIFY `id_habilitacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_acesso`
--
ALTER TABLE `historico_acesso`
  MODIFY `id_historico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_alteracoes`
--
ALTER TABLE `historico_alteracoes`
  MODIFY `id_alteracao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_relatorio`
--
ALTER TABLE `historico_relatorio`
  MODIFY `id_historico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_tarefa`
--
ALTER TABLE `historico_tarefa`
  MODIFY `id_historico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `log_sistema`
--
ALTER TABLE `log_sistema`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `metadado`
--
ALTER TABLE `metadado`
  MODIFY `id_metadado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `modelo_documento`
--
ALTER TABLE `modelo_documento`
  MODIFY `id_modelo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `modelo_email`
--
ALTER TABLE `modelo_email`
  MODIFY `id_modelo_email` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacao`
--
ALTER TABLE `notificacao`
  MODIFY `id_notificacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `perfilacesso`
--
ALTER TABLE `perfilacesso`
  MODIFY `id_perfil_acesso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `permissoes`
--
ALTER TABLE `permissoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `pesquisa_salva`
--
ALTER TABLE `pesquisa_salva`
  MODIFY `id_pesquisa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `recursos_humanos`
--
ALTER TABLE `recursos_humanos`
  MODIFY `id_rh` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `relacionamento`
--
ALTER TABLE `relacionamento`
  MODIFY `id_relacionamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `relatorio_personalizado`
--
ALTER TABLE `relatorio_personalizado`
  MODIFY `id_relatorio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `traducao`
--
ALTER TABLE `traducao`
  MODIFY `id_traducao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `utilizador`
--
ALTER TABLE `utilizador`
  MODIFY `id_utilizador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `viatura`
--
ALTER TABLE `viatura`
  MODIFY `id_viatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `administrador`
--
ALTER TABLE `administrador`
  ADD CONSTRAINT `fk_administrador_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `alerta`
--
ALTER TABLE `alerta`
  ADD CONSTRAINT `alerta_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE CASCADE,
  ADD CONSTRAINT `alerta_ibfk_2` FOREIGN KEY (`id_equipa`) REFERENCES `equipa` (`id_equipa`) ON DELETE CASCADE,
  ADD CONSTRAINT `alerta_ibfk_3` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`) ON DELETE CASCADE,
  ADD CONSTRAINT `alerta_ibfk_4` FOREIGN KEY (`id_responsavel`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `alerta_ibfk_5` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `alerta_ibfk_6` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_alerta_utilizador` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`),
  ADD CONSTRAINT `fk_alerta_utilizador_atualizacao` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`);

--
-- Limitadores para a tabela `alerta_colaborador`
--
ALTER TABLE `alerta_colaborador`
  ADD CONSTRAINT `fk_alerta_colaborador_alerta` FOREIGN KEY (`id_alerta`) REFERENCES `alerta` (`id_alerta`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_alerta_colaborador_colaborador` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `api_acesso`
--
ALTER TABLE `api_acesso`
  ADD CONSTRAINT `fk_api_acesso_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `cache`
--
ALTER TABLE `cache`
  ADD CONSTRAINT `fk_cache_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `campos_personalizados_historico`
--
ALTER TABLE `campos_personalizados_historico`
  ADD CONSTRAINT `campos_personalizados_historico_ibfk_1` FOREIGN KEY (`campo_id`) REFERENCES `campos_personalizados` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `campo_personalizado_valor`
--
ALTER TABLE `campo_personalizado_valor`
  ADD CONSTRAINT `campo_personalizado_valor_ibfk_1` FOREIGN KEY (`id_campo`) REFERENCES `campo_personalizado` (`id_campo`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `colaborador`
--
ALTER TABLE `colaborador`
  ADD CONSTRAINT `colaborador_ibfk_1` FOREIGN KEY (`id_funcao`) REFERENCES `funcao` (`id_funcao`),
  ADD CONSTRAINT `colaborador_ibfk_2` FOREIGN KEY (`id_equipa`) REFERENCES `equipa` (`id_equipa`),
  ADD CONSTRAINT `colaborador_ibfk_3` FOREIGN KEY (`id_gestor_direto`) REFERENCES `colaborador` (`id_colaborador`);

--
-- Limitadores para a tabela `configuracao`
--
ALTER TABLE `configuracao`
  ADD CONSTRAINT `configuracao_ibfk_1` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `configuracao_ibfk_2` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `contato_emergencia`
--
ALTER TABLE `contato_emergencia`
  ADD CONSTRAINT `contato_emergencia_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `convidado`
--
ALTER TABLE `convidado`
  ADD CONSTRAINT `fk_convidado_responsavel` FOREIGN KEY (`responsavel`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_convidado_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `coordenador`
--
ALTER TABLE `coordenador`
  ADD CONSTRAINT `fk_coordenador_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_coordenador_equipa` FOREIGN KEY (`id_equipa`) REFERENCES `equipa` (`id_equipa`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_coordenador_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_coordenador_utilizador_atualizacao` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_coordenador_utilizador_criacao` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `dashboard_widget`
--
ALTER TABLE `dashboard_widget`
  ADD CONSTRAINT `fk_dashboard_widget_dashboard` FOREIGN KEY (`id_dashboard`) REFERENCES `dashboard` (`id_dashboard`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `documento`
--
ALTER TABLE `documento`
  ADD CONSTRAINT `documento_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE CASCADE,
  ADD CONSTRAINT `documento_ibfk_2` FOREIGN KEY (`id_aprovador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `documento_ibfk_3` FOREIGN KEY (`id_utilizador_upload`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `equipa`
--
ALTER TABLE `equipa`
  ADD CONSTRAINT `equipa_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `equipa_ibfk_2` FOREIGN KEY (`id_equipa_pai`) REFERENCES `equipa` (`id_equipa`),
  ADD CONSTRAINT `equipa_ibfk_3` FOREIGN KEY (`id_coordenador`) REFERENCES `colaborador` (`id_colaborador`);

--
-- Limitadores para a tabela `favorito`
--
ALTER TABLE `favorito`
  ADD CONSTRAINT `favorito_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `ferias`
--
ALTER TABLE `ferias`
  ADD CONSTRAINT `ferias_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE CASCADE,
  ADD CONSTRAINT `ferias_ibfk_2` FOREIGN KEY (`id_aprovador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `ferias_ibfk_3` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `ferias_ibfk_4` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `fila_email`
--
ALTER TABLE `fila_email`
  ADD CONSTRAINT `fila_email_ibfk_1` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `fila_impressao`
--
ALTER TABLE `fila_impressao`
  ADD CONSTRAINT `fk_fila_impressao_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `filtro_salvo`
--
ALTER TABLE `filtro_salvo`
  ADD CONSTRAINT `filtro_salvo_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `funcao`
--
ALTER TABLE `funcao`
  ADD CONSTRAINT `funcao_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`);

--
-- Limitadores para a tabela `habilitacao`
--
ALTER TABLE `habilitacao`
  ADD CONSTRAINT `habilitacao_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE CASCADE,
  ADD CONSTRAINT `habilitacao_ibfk_2` FOREIGN KEY (`anexo_certificado`) REFERENCES `documento` (`id_documento`) ON DELETE SET NULL,
  ADD CONSTRAINT `habilitacao_ibfk_3` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `habilitacao_ibfk_4` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `historico_acesso`
--
ALTER TABLE `historico_acesso`
  ADD CONSTRAINT `historico_acesso_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `historico_alteracoes`
--
ALTER TABLE `historico_alteracoes`
  ADD CONSTRAINT `historico_alteracoes_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `historico_relatorio`
--
ALTER TABLE `historico_relatorio`
  ADD CONSTRAINT `fk_historico_relatorio_relatorio` FOREIGN KEY (`id_relatorio`) REFERENCES `relatorio_personalizado` (`id_relatorio`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_historico_relatorio_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `historico_tarefa`
--
ALTER TABLE `historico_tarefa`
  ADD CONSTRAINT `historico_tarefa_ibfk_1` FOREIGN KEY (`id_tarefa`) REFERENCES `tarefa_agendada` (`id_tarefa`) ON DELETE CASCADE,
  ADD CONSTRAINT `historico_tarefa_ibfk_2` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `log_sistema`
--
ALTER TABLE `log_sistema`
  ADD CONSTRAINT `log_sistema_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `modelo_documento`
--
ALTER TABLE `modelo_documento`
  ADD CONSTRAINT `modelo_documento_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`) ON DELETE SET NULL,
  ADD CONSTRAINT `modelo_documento_ibfk_2` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `modelo_documento_ibfk_3` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `modelo_email`
--
ALTER TABLE `modelo_email`
  ADD CONSTRAINT `modelo_email_ibfk_1` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `modelo_email_ibfk_2` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `notificacao`
--
ALTER TABLE `notificacao`
  ADD CONSTRAINT `notificacao_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacao_ibfk_2` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `pesquisa_salva`
--
ALTER TABLE `pesquisa_salva`
  ADD CONSTRAINT `pesquisa_salva_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `recursos_humanos`
--
ALTER TABLE `recursos_humanos`
  ADD CONSTRAINT `fk_rh_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `relatorio_personalizado`
--
ALTER TABLE `relatorio_personalizado`
  ADD CONSTRAINT `fk_relatorio_personalizado_utilizador` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `sessao`
--
ALTER TABLE `sessao`
  ADD CONSTRAINT `fk_sessao_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `traducao`
--
ALTER TABLE `traducao`
  ADD CONSTRAINT `fk_traducao_utilizador` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `utilizador`
--
ALTER TABLE `utilizador`
  ADD CONSTRAINT `utilizador_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE SET NULL,
  ADD CONSTRAINT `utilizador_ibfk_2` FOREIGN KEY (`id_perfil_acesso`) REFERENCES `perfilacesso` (`id_perfil_acesso`);

--
-- Limitadores para a tabela `viatura`
--
ALTER TABLE `viatura`
  ADD CONSTRAINT `fk_viatura_colaborador` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_viatura_utilizador_atualizacao` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_viatura_utilizador_criacao` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `viatura_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE SET NULL,
  ADD CONSTRAINT `viatura_ibfk_2` FOREIGN KEY (`id_utilizador_criacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL,
  ADD CONSTRAINT `viatura_ibfk_3` FOREIGN KEY (`id_utilizador_atualizacao`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
