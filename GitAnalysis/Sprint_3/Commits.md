# Commits by author
#### 1230769@isep.ipp.pt
<details>
<summary>Diff</summary>

<pre>
 DAL/database.php                    |    3 
 b/BLL/AlertaBLL.php                 |   98 +
 b/BLL/PerfilAcessoBLL.php           |   84 +
 b/BLL/UtilizadorBLL.php             |  101 +
 b/BLL/equipaBLL.php                 |  328 +++++
 b/DAL/AlertaDAL.php                 |  171 +++
 b/DAL/PerfilAcessoDAL.php           |  184 +++
 b/DAL/UtilizadorDAL.php             |  320 +++++
 b/DAL/campo_personalizadoDAL.php    |    2 
 b/DAL/colaboradorDAL.php            |    1 
 b/DAL/database.php                  |   14 
 b/DAL/equipaDAL.php                 |  184 ++!
 b/DAL/loginDAL.php                  |    2 
 b/Model/Alerta.php                  |   57 +
 b/UI/admin/ajuda.php                |    1 
 b/UI/admin/api_equipas.php          |    4 
 b/UI/admin/atualizar_perfil.php     |  118 ++
 b/UI/admin/editar_perfil.php        |  406 +++++++
 b/UI/admin/equipa_dashboard.php     |  539 +++++++++
 b/UI/admin/equipa_detalhes.php      |    8 
 b/UI/admin/equipas.php              | 1444 -----------------!!!!!!!!
 b/UI/admin/excluir_perfil.php       |   62 +
 b/UI/admin/ficha_colaboradoress.sql | 2033 ++++++++++++++++++++++++++++++++++++
 b/UI/admin/includes/sidebar.php     |   17 
 b/UI/admin/obter_membros_equipa.php |    4 
 b/UI/admin/perfis.php               |  896 ----!!!!!!!!!!!
 b/UI/admin/processa_perfil.php      |  118 ++
 b/UI/admin/usuarios.php             |  354 ++++!!
 b/UI/assets/img/avatars/default.png |binary
 b/UI/header.php                     |   29 
 b/UI/includes/verificar_acesso.php  |   34 
 b/autoload.php                      |   16 
 b/verificar_colaboradores.php       |   33 
 b/verificar_estrutura.php           |   28 
 b/verificar_usuarios.php            |   34 
 35 files changed, 5222 insertions(+), 1251 deletions(-), 1254 modifications(!)
</pre>
</details>
<details>
<summary>Commits</summary>

<pre>
commit eb365e05c04c089731055d035c811819b323141e	refs/heads/main
Author: Francisca Moreira <1230769@isep.ipp.pt>
Date:   Wed Jul 2 10:29:21 2025 +0100

    atualizaçao da pagina perfis e equipas

A	BLL/AlertaBLL.php
A	BLL/PerfilAcessoBLL.php
M	BLL/UtilizadorBLL.php
M	BLL/equipaBLL.php
A	DAL/AlertaDAL.php
A	DAL/PerfilAcessoDAL.php
M	DAL/UtilizadorDAL.php
M	DAL/campo_personalizadoDAL.php
M	DAL/colaboradorDAL.php
M	DAL/database.php
M	DAL/equipaDAL.php
M	DAL/loginDAL.php
A	Model/Alerta.php
M	UI/admin/ajuda.php
M	UI/admin/api_equipas.php
A	UI/admin/atualizar_perfil.php
A	UI/admin/editar_perfil.php
A	UI/admin/equipa_dashboard.php
M	UI/admin/equipa_detalhes.php
M	UI/admin/equipas.php
A	UI/admin/excluir_perfil.php
A	UI/admin/ficha_colaboradoress.sql
M	UI/admin/includes/sidebar.php
M	UI/admin/obter_membros_equipa.php
M	UI/admin/perfis.php
A	UI/admin/processa_perfil.php
M	UI/admin/usuarios.php
A	UI/assets/img/avatars/default.png
M	UI/header.php
M	UI/includes/verificar_acesso.php
M	autoload.php
A	verificar_colaboradores.php
A	verificar_estrutura.php
A	verificar_usuarios.php

commit a623c01c74dd273d498c62988f2a120c4c2857ed	refs/heads/main
Author: Francisca Moreira <1230769@isep.ipp.pt>
Date:   Wed Jul 2 10:27:38 2025 +0100

    Atualizaçoes da pagina equipa e de perfis

M	DAL/database.php
</pre>

</details>

#### 1230806@isep.ipp.pt
<details>
<summary>Diff</summary>

<pre>
 UI/colaborador.php      |  623 +++++++++++++++++++++---!!!!!!!!!!!!!!!!!!!!!!!
 UI/processa_login.php   |    4 
 b/BLL/loginBLL.php      |    8 
 b/DAL/loginDAL.php      |   15 !
 b/UI/colaborador.php    |    1 
 b/UI/js/colaborador.js  |  134 -!!!!!!!!!
 b/UI/processa_login.php |    2 
 b/api/colaborador.php   |   58 ++++
 b/config/perfis.php     |    5 
 9 files changed, 354 insertions(+), 63 deletions(-), 433 modifications(!)
</pre>
</details>
<details>
<summary>Commits</summary>

<pre>
commit ea42fdfdab9f398ac880bfe3d30c4b84a5bb00fd	refs/heads/main
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Sun Jul 6 23:41:49 2025 +0100

    atualizações página colaborador

M	UI/colaborador.php

commit 21d5120233825ca043d07e5e9d580454504fb712	refs/heads/main
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Fri Jul 4 12:36:36 2025 +0100

    novas atualizações página colaborador

M	UI/colaborador.php
A	api/colaborador.php

commit 8693590937f001f019a8e338f5083c5d70a51cf0	refs/heads/main
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Fri Jul 4 11:50:54 2025 +0100

    atualizações página colaborador

M	UI/colaborador.php
M	UI/js/colaborador.js

commit c876f6b991923e263cbadf64988df8f965ef4707	refs/heads/main
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Fri Jul 4 08:57:41 2025 +0100

    redirecionamento pagina login

M	UI/processa_login.php

commit 7d056f740b23dd43297897a2be92ee10d3f78281	refs/heads/main
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Mon Jun 30 14:30:25 2025 +0100

    resolução login

M	BLL/loginBLL.php
M	DAL/loginDAL.php
M	UI/processa_login.php
M	config/perfis.php

commit 905d0f4a5929deec56edda8c8fe36d04f90e6cd8	refs/remotes/origin/nova-branch
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Mon Jun 30 13:21:35 2025 +0100

    Página colaborador funcional + logins

M	BLL/loginBLL.php
M	DAL/loginDAL.php
M	UI/processa_login.php
M	config/perfis.php

commit 67985ffe92815b1767aeea53573a14d37efdc56d	refs/remotes/origin/nova-branch
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Mon Jun 30 11:18:26 2025 +0100

    página de coordenador e login funcional para 3 páginas

A	DAL/equipe.php
M	DAL/loginDAL.php
A	UI/coordenador.php
M	UI/processa_login.php
A	config/alertas_config.php
A	config/config.php
M	config/perfis.php
A	scripts/gerar_alertas.php
A	scripts/verificar_perfil_coordenador.php
A	services/coordenador/alertas.php
A	services/coordenador/equipe.php
</pre>

</details>

#### 1231043@isep.ipp.pt
<details>
<summary>Diff</summary>

<pre>
 /UI/administrador.php  |   93 +++++++++++++++++++++++++++++++++
 b/UI/.DS_Store         |binary
 b/UI/administrador.php |   83 +++++++++++++++++++++++++!!!!
 b/UI/graficos.html     |  137 +++++++++++++++++++++++++++++++++++++++++++++++++
 b/UI/logs/debug.log    |    7 ++
 b/UI/recursos.php      |   56 ++++++++++++++++++++
 6 files changed, 363 insertions(+), 13 modifications(!)
</pre>
</details>
<details>
<summary>Commits</summary>

<pre>
commit a6a5ce603130780a018ece9037bee3c8e4fd7474	refs/heads/main
Author: Beatriz Vieira <1231043@isep.ipp.pt>
Date:   Fri Jul 4 14:24:08 2025 +0100

    admin

M	UI/administrador.php
M	UI/logs/debug.log

commit 8fa850b4d3bc8e7a5d3ca5922a533c697f87f276	refs/heads/main
Author: Beatriz Vieira <1231043@isep.ipp.pt>
Date:   Fri Jul 4 12:44:26 2025 +0100

    Administrador

A	UI/.DS_Store
A	UI/administrador.php
A	UI/graficos.html
A	UI/recursos.php
</pre>

</details>

#### 1231693@isep.ipp.pt
<details>
<summary>Diff</summary>

<pre>
 /UI/enviar-convite.php                                                       |  214 
 UI/convidado.php                                                             |  425 
 b/DAL/PHPMailerConfig.php                                                    |   93 
 b/UI/convidado.php                                                           |   39 
 b/UI/enviar-convite.php                                                      |   82 
 b/logs/email_errors.log                                                      |   13 
 b/mail/.DS_Store                                                             |binary
 b/mail/PHPMailer-master/.DS_Store                                            |binary
 b/mail/PHPMailer-master/PHPMailer-master/COMMITMENT                          |   46 
 b/mail/PHPMailer-master/PHPMailer-master/LICENSE                             |  502 
 b/mail/PHPMailer-master/PHPMailer-master/README.md                           |  232 
 b/mail/PHPMailer-master/PHPMailer-master/SECURITY.md                         |   37 
 b/mail/PHPMailer-master/PHPMailer-master/SMTPUTF8.md                         |   48 
 b/mail/PHPMailer-master/PHPMailer-master/VERSION                             |    1 
 b/mail/PHPMailer-master/PHPMailer-master/composer.json                       |   81 
 b/mail/PHPMailer-master/PHPMailer-master/get_oauth_token.php                 |  182 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-af.php      |   26 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ar.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-as.php      |   35 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-az.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ba.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-be.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-bg.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-bn.php      |   35 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ca.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-cs.php      |   28 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-da.php      |   36 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-de.php      |   28 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-el.php      |   33 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-eo.php      |   26 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-es.php      |   36 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-et.php      |   28 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-fa.php      |   28 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-fi.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-fo.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-fr.php      |   36 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-gl.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-he.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-hi.php      |   35 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-hr.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-hu.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-hy.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-id.php      |   31 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-it.php      |   28 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ja.php      |   37 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ka.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ko.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ku.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-lt.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-lv.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-mg.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-mn.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ms.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-nb.php      |   33 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-nl.php      |   34 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-pl.php      |   33 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-pt.php      |   34 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-pt_br.php   |   38 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ro.php      |   33 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ru.php      |   36 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-si.php      |   34 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-sk.php      |   30 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-sl.php      |   36 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-sr.php      |   28 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-sr_latn.php |   28 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-sv.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-tl.php      |   28 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-tr.php      |   38 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-uk.php      |   28 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ur.php      |   30 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-vi.php      |   27 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-zh.php      |   29 
 b/mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-zh_cn.php   |   36 
 b/mail/PHPMailer-master/PHPMailer-master/src/DSNConfigurator.php             |  245 
 b/mail/PHPMailer-master/PHPMailer-master/src/Exception.php                   |   40 
 b/mail/PHPMailer-master/PHPMailer-master/src/OAuth.php                       |  139 
 b/mail/PHPMailer-master/PHPMailer-master/src/OAuthTokenProvider.php          |   44 
 b/mail/PHPMailer-master/PHPMailer-master/src/PHPMailer.php                   | 5366 ++++++++++
 b/mail/PHPMailer-master/PHPMailer-master/src/POP3.php                        |  469 
 b/mail/PHPMailer-master/PHPMailer-master/src/SMTP.php                        | 1578 ++
 b/mail/SendEmail.php                                                         |   92 
 b/phpmailer.zip                                                              |binary
 b/testar_envio_email.php                                                     |   49 
 b/vendor/PHPMailer-6.8.1/COMMITMENT                                          |   46 
 b/vendor/PHPMailer-6.8.1/LICENSE                                             |  502 
 b/vendor/PHPMailer-6.8.1/README.md                                           |  230 
 b/vendor/PHPMailer-6.8.1/SECURITY.md                                         |   37 
 b/vendor/PHPMailer-6.8.1/VERSION                                             |    1 
 b/vendor/PHPMailer-6.8.1/composer.json                                       |   78 
 b/vendor/PHPMailer-6.8.1/get_oauth_token.php                                 |  182 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-af.php                      |   26 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-ar.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-az.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-ba.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-be.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-bg.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-ca.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-cs.php                      |   28 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-da.php                      |   35 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-de.php                      |   28 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-el.php                      |   33 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-eo.php                      |   26 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-es.php                      |   31 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-et.php                      |   28 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-fa.php                      |   28 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-fi.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-fo.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-fr.php                      |   37 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-gl.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-he.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-hi.php                      |   35 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-hr.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-hu.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-hy.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-id.php                      |   31 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-it.php                      |   28 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-ja.php                      |   29 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-ka.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-ko.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-lt.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-lv.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-mg.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-mn.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-ms.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-nb.php                      |   33 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-nl.php                      |   34 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-pl.php                      |   26 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-pt.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-pt_br.php                   |   38 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-ro.php                      |   33 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-ru.php                      |   28 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-si.php                      |   34 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-sk.php                      |   30 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-sl.php                      |   36 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-sr.php                      |   28 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-sr_latn.php                 |   28 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-sv.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-tl.php                      |   28 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-tr.php                      |   31 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-uk.php                      |   28 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-vi.php                      |   27 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-zh.php                      |   29 
 b/vendor/PHPMailer-6.8.1/language/phpmailer.lang-zh_cn.php                   |   36 
 b/vendor/PHPMailer-6.8.1/src/DSNConfigurator.php                             |  245 
 b/vendor/PHPMailer-6.8.1/src/Exception.php                                   |   40 
 b/vendor/PHPMailer-6.8.1/src/OAuth.php                                       |  139 
 b/vendor/PHPMailer-6.8.1/src/OAuthTokenProvider.php                          |   44 
 b/vendor/PHPMailer-6.8.1/src/PHPMailer.php                                   | 5126 +++++++++
 b/vendor/PHPMailer-6.8.1/src/POP3.php                                        |  467 
 b/vendor/PHPMailer-6.8.1/src/SMTP.php                                        | 1466 ++
 b/vendor/phpmailer/phpmailer/src/DSNConfigurator.php                         |  245 
 b/vendor/phpmailer/phpmailer/src/Exception.php                               |   40 
 b/vendor/phpmailer/phpmailer/src/OAuth.php                                   |  139 
 b/vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php                      |   44 
 b/vendor/phpmailer/phpmailer/src/PHPMailer.php                               | 5366 ++++++++++
 b/vendor/phpmailer/phpmailer/src/POP3.php                                    |  469 
 b/vendor/phpmailer/phpmailer/src/SMTP.php                                    | 1578 ++
 157 files changed, 29675 insertions(+), 38 deletions(-), 45 modifications(!)
</pre>
</details>
<details>
<summary>Commits</summary>

<pre>
commit b2d20f78be215bfd068c01df1016f54e1bef97da	refs/heads/main
Author: Catarina Cardoso <1231693@isep.ipp.pt>
Date:   Fri Jul 4 13:09:20 2025 +0100

    convidado link

A	DAL/PHPMailerConfig.php
M	UI/enviar-convite.php
A	logs/email_errors.log
A	mail/.DS_Store
A	mail/PHPMailer-master/.DS_Store
A	mail/PHPMailer-master/PHPMailer-master/COMMITMENT
A	mail/PHPMailer-master/PHPMailer-master/LICENSE
A	mail/PHPMailer-master/PHPMailer-master/README.md
A	mail/PHPMailer-master/PHPMailer-master/SECURITY.md
A	mail/PHPMailer-master/PHPMailer-master/SMTPUTF8.md
A	mail/PHPMailer-master/PHPMailer-master/VERSION
A	mail/PHPMailer-master/PHPMailer-master/composer.json
A	mail/PHPMailer-master/PHPMailer-master/get_oauth_token.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-af.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ar.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-as.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-az.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ba.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-be.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-bg.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-bn.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ca.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-cs.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-da.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-de.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-el.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-eo.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-es.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-et.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-fa.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-fi.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-fo.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-fr.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-gl.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-he.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-hi.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-hr.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-hu.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-hy.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-id.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-it.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ja.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ka.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ko.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ku.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-lt.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-lv.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-mg.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-mn.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ms.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-nb.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-nl.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-pl.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-pt.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-pt_br.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ro.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ru.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-si.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-sk.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-sl.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-sr.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-sr_latn.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-sv.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-tl.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-tr.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-uk.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-ur.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-vi.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-zh.php
A	mail/PHPMailer-master/PHPMailer-master/language/phpmailer.lang-zh_cn.php
A	mail/PHPMailer-master/PHPMailer-master/src/DSNConfigurator.php
A	mail/PHPMailer-master/PHPMailer-master/src/Exception.php
A	mail/PHPMailer-master/PHPMailer-master/src/OAuth.php
A	mail/PHPMailer-master/PHPMailer-master/src/OAuthTokenProvider.php
A	mail/PHPMailer-master/PHPMailer-master/src/PHPMailer.php
A	mail/PHPMailer-master/PHPMailer-master/src/POP3.php
A	mail/PHPMailer-master/PHPMailer-master/src/SMTP.php
A	mail/SendEmail.php
A	phpmailer.zip
A	testar_envio_email.php
A	vendor/PHPMailer-6.8.1/COMMITMENT
A	vendor/PHPMailer-6.8.1/LICENSE
A	vendor/PHPMailer-6.8.1/README.md
A	vendor/PHPMailer-6.8.1/SECURITY.md
A	vendor/PHPMailer-6.8.1/VERSION
A	vendor/PHPMailer-6.8.1/composer.json
A	vendor/PHPMailer-6.8.1/get_oauth_token.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-af.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-ar.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-az.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-ba.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-be.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-bg.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-ca.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-cs.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-da.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-de.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-el.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-eo.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-es.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-et.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-fa.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-fi.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-fo.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-fr.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-gl.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-he.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-hi.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-hr.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-hu.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-hy.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-id.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-it.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-ja.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-ka.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-ko.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-lt.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-lv.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-mg.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-mn.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-ms.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-nb.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-nl.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-pl.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-pt.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-pt_br.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-ro.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-ru.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-si.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-sk.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-sl.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-sr.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-sr_latn.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-sv.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-tl.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-tr.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-uk.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-vi.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-zh.php
A	vendor/PHPMailer-6.8.1/language/phpmailer.lang-zh_cn.php
A	vendor/PHPMailer-6.8.1/src/DSNConfigurator.php
A	vendor/PHPMailer-6.8.1/src/Exception.php
A	vendor/PHPMailer-6.8.1/src/OAuth.php
A	vendor/PHPMailer-6.8.1/src/OAuthTokenProvider.php
A	vendor/PHPMailer-6.8.1/src/PHPMailer.php
A	vendor/PHPMailer-6.8.1/src/POP3.php
A	vendor/PHPMailer-6.8.1/src/SMTP.php
A	vendor/phpmailer/phpmailer/src/DSNConfigurator.php
A	vendor/phpmailer/phpmailer/src/Exception.php
A	vendor/phpmailer/phpmailer/src/OAuth.php
A	vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php
A	vendor/phpmailer/phpmailer/src/PHPMailer.php
A	vendor/phpmailer/phpmailer/src/POP3.php
A	vendor/phpmailer/phpmailer/src/SMTP.php

commit 4e1a3d1ba928a469eabc2afbfba0335351a82f94	refs/heads/main
Author: Catarina Cardoso <1231693@isep.ipp.pt>
Date:   Mon Jun 30 16:59:36 2025 +0100

    convidado e link

M	UI/convidado.php
A	UI/enviar-convite.php

commit 4785b70cd1f4d1f566102aa39e672f290df2f50d	refs/heads/main
Author: Catarina Cardoso <1231693@isep.ipp.pt>
Date:   Mon Jun 30 15:39:50 2025 +0100

    convidado

M	UI/convidado.php

commit cf0ce0ec40049ded23ee0662dd50939e9cf75a42	refs/remotes/origin/nova-branch
Author: Catarina Cardoso <1231693@isep.ipp.pt>
Date:   Mon Jun 30 11:08:43 2025 +0100

    s

A	BLL/ColaboradorBLL.php
M	BLL/loginBLL.php
M	DAL/database.php
M	DAL/loginDAL.php
A	UI/admin/dashboard_colaboradores.php
M	UI/admin/includes/sidebar.php
M	UI/colaborador.php
M	UI/dashboard.php
M	UI/processa_login.php
M	UI/rh.php
A	UI/rh/colaboradores.php
A	UI/rh/dashboard.php
A	UI/rh/dashboard_colaboradores.php
A	UI/rh/includes/sidebar.php
A	config/database_config.php

commit 37ed741638f4e5015adbdcbb50e2e4ef6926ea6a	refs/remotes/origin/nova-branch
Author: Catarina Cardoso <1231693@isep.ipp.pt>
Date:   Sat Jun 28 11:54:49 2025 +0100

    c

A	BLL/guestBLL.php
M	UI/convidado.php
</pre>

</details>

