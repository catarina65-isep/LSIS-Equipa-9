# Commits by author
#### 1230769@isep.ipp.pt
<details>
<summary>Diff</summary>

<pre>
 /BLL/loginBLL.php                         |   56 +
 /DAL/database.php                         |   29 
 /DAL/loginDAL.php                         |  121 ++
 /UI/footer.php                            |    3 
 /UI/header.php                            |   39 
 /UI/login.php                             |  116 ++
 /UI/processa_login.php                    |   61 +
 DAL/Auth.php                              |   58 -
 UI/BLL/LoginBLL.php                       |   56 -
 UI/DAL/LoginDAL.php                       |  121 --
 UI/DAL/database.php                       |   28 
 UI/acessoConvidado.html                   |  380 -------
 UI/login.php                              |  462 ++++++++
 UI/pagina_recursos.php                    |   75 -
 UI/processa_login.php                     |  125 !!
 applications.html                         |   79 -
 auth/conexao.php                          |   12 
 b/.DS_Store                               |binary
 b/BLL/campoPersonalizadoBLL.php           |  243 ++++
 b/BLL/loginBLL.php                        |    3 
 b/DAL/campo_personalizadoDAL.php          |  198 ++++
 b/DAL/colaboradorDAL.php                  |   27 
 b/DAL/config.php                          |   34 
 b/DAL/database.php                        |   21 
 b/DAL/loginDAL.php                        |    2 
 b/UI/BLL/LoginBLL.php                     |   56 +
 b/UI/DAL/LoginDAL.php                     |  121 ++
 b/UI/DAL/database.php                     |   28 
 b/UI/acessoConvidado.html                 |  380 +++++++
 b/UI/admin/ajuda.php                      |  860 +++++++++++++++++
 b/UI/admin/api_campos_personalizados.php  |   76 +
 b/UI/admin/campos_personalizados.php      | 1477 ++++++++++++++++++++++++++++++
 b/UI/admin/campos_personalizados_fixed.js |   49 
 b/UI/admin/colaboradores.php              |  777 +++++++++++++++
 b/UI/admin/dashboard.php                  |  783 +++++++++++++++
 b/UI/admin/includes/sidebar.php           |  211 ++++
 b/UI/admin/perfis.php                     | 1085 ++++++++++++++++++++++
 b/UI/admin/relatorios.php                 |  506 ++++++++++
 b/UI/admin/usuarios.php                   |  367 +++++++
 b/UI/assets/img/logos/tlantic-logo.jpg    |binary
 b/UI/footer.php                           |  365 +++++++
 b/UI/header.php                           |  282 +++++
 b/UI/index.php                            |    5 
 b/UI/login.html                           |  533 ++++++++++
 b/UI/login.php                            |  432 ++++++++
 b/UI/logout.php                           |   14 
 b/UI/processa_login.php                   |    8 
 b/UI/processa_registo.php                 |   63 +
 b/UI/recuperar_senha.php                  |   96 +
 b/UI/registo.html                         |  411 ++++++++
 b/UI/style.css                            |   36 
 b/index.php                               |   66 +
 b/script.js                               |   97 +
 db.php                                    |    6 
 login.html                                |  487 ---------
 55 files changed, 10480 insertions(+), 1349 deletions(-), 197 modifications(!)
</pre>
</details>
<details>
<summary>Commits</summary>

<pre>
commit 4f068033e2c16f8d7cda05694e83d00295bf0ba4	refs/heads/main
Author: Francisca Moreira <1230769@isep.ipp.pt>
Date:   Thu Jun 26 17:30:34 2025 +0100

    pagina administrador

A	BLL/campoPersonalizadoBLL.php
M	BLL/loginBLL.php
A	DAL/campo_personalizadoDAL.php
M	DAL/loginDAL.php
A	UI/admin/ajuda.php
A	UI/admin/api_campos_personalizados.php
A	UI/admin/campos_personalizados.php
A	UI/admin/campos_personalizados_fixed.js
A	UI/admin/colaboradores.php
A	UI/admin/dashboard.php
A	UI/admin/includes/sidebar.php
A	UI/admin/perfis.php
A	UI/admin/relatorios.php
A	UI/admin/usuarios.php
A	UI/assets/img/logos/tlantic-logo.jpg
M	UI/footer.php
M	UI/header.php
M	UI/processa_login.php

commit 468a14891528565f19cfca15323bb6baee5f7909	refs/heads/main
Author: Francisca Moreira <1230769@isep.ipp.pt>
Date:   Wed Jun 25 16:17:30 2025 +0100

    login atualizaçao

D	UI/acessoConvidado.html
M	UI/login.php
A	UI/logout.php
M	UI/processa_login.php
M	index.php

commit 639ccfc583fa6633a07d773569d94e4a89734b28	refs/heads/main
Author: Francisca Moreira <1230769@isep.ipp.pt>
Date:   Wed Jun 25 13:06:24 2025 +0100

    novas alteracões

A	.DS_Store
D	DAL/Auth.php
D	UI/pagina_recursos.php
D	applications.html
D	auth/conexao.php
D	db.php

commit b07cb021e0cce30172d0a18dfb4d9bf140919f9d	refs/heads/main
Author: Francisca Moreira <1230769@isep.ipp.pt>
Date:   Wed Jun 25 13:01:18 2025 +0100

    login certo agora

M	UI/login.php
M	UI/processa_login.php

commit e6aa7130fd78a0e7b7d43dcbbc2948e05b6a20c0	refs/heads/main
Author: Francisca Moreira <1230769@isep.ipp.pt>
Date:   Wed Jun 25 12:42:14 2025 +0100

    atualizacao

D	UI/BLL/LoginBLL.php
D	UI/DAL/LoginDAL.php
D	UI/DAL/database.php

commit 4327d07e1d4ade0473f143d4e5a05f4d9f4faa57	refs/heads/main
Author: Francisca Moreira <1230769@isep.ipp.pt>
Date:   Wed Jun 25 12:33:34 2025 +0100

    atualizacao do login

A	BLL/loginBLL.php
M	DAL/database.php
A	DAL/loginDAL.php
A	UI/BLL/LoginBLL.php
A	UI/DAL/LoginDAL.php
A	UI/DAL/database.php
A	UI/index.php
M	UI/processa_login.php

commit 2a7963f7f27539a3d8527b7823f9b12800190bd7	refs/heads/main
Author: Francisca Moreira <1230769@isep.ipp.pt>
Date:   Wed Jun 25 12:11:11 2025 +0100

    atualizacao de login.php

M	UI/login.php

commit 6d64c9256dac214cde12eb5c90b4bef4f0024db9	refs/heads/main
Author: Francisca Moreira <1230769@isep.ipp.pt>
Date:   Wed Jun 25 11:29:13 2025 +0100

    novas atualizaçoes

A	DAL/colaboradorDAL.php
A	DAL/config.php
A	DAL/database.php
A	UI/acessoConvidado.html
A	UI/footer.php
A	UI/header.php
A	UI/login.html
A	UI/login.php
A	UI/processa_login.php
A	UI/processa_registo.php
A	UI/recuperar_senha.php
A	UI/registo.html
A	UI/style.css
D	login.html
A	script.js
</pre>

</details>

#### 1230806@isep.ipp.pt
<details>
<summary>Diff</summary>

<pre>
 /DAL/colaborador.php      |   55 +
 /UI/colaborador.php       | 1374 +++++++++++++++++++++++++++++++++++
 /UI/js/colaborador.js     |  443 +++++++++++
 /js/colaborador.js        |   57 +
 UI/colaborador.php        | 1771 ++++++++------------------------!!!!!!!!!!!!!
 UI/js/colaborador.js      |  120 ++!
 UI/login.html             |  533 -------------
 b/BLL/colaborador.php     |  117 +++
 b/DAL/colaborador.php     |   38 
 b/DAL/documentos.php      |   89 ++
 b/DAL/loginDAL.php        |   18 
 b/UI/colaborador.php      |  357 ++++--!!
 b/UI/footer.php           |   60 -
 b/UI/header.php           |  109 ++
 b/UI/includes/session.php |    5 
 b/UI/js/colaborador.js    |   51 +
 b/UI/login.php            |    1 
 b/UI/logout.php           |    2 
 b/UI/processa_login.php   |    3 
 b/config/perfis.php       |   16 
 b/js/colaborador.js       |   53 +
 b/logout.php              |    9 
 22 files changed, 3020 insertions(+), 1604 deletions(-), 657 modifications(!)
</pre>
</details>
<details>
<summary>Commits</summary>

<pre>
commit 3c48702d5fd45f7cd48ff5a6ef002876b534fdb3	refs/heads/main (HEAD -> main, origin/main, origin/HEAD)
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Sun Jun 29 23:09:03 2025 +0100

    Página colaborador

M	UI/colaborador.php
M	UI/js/colaborador.js

commit 5030da6fdca128e4d00108a6e9ba31848e97295b	refs/heads/main
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Sun Jun 29 22:30:29 2025 +0100

    + atualizações pagina colaborador

M	DAL/colaborador.php
A	DAL/documentos.php
M	UI/colaborador.php
M	UI/js/colaborador.js
D	UI/login.html
M	UI/logout.php
A	logout.php

commit 0f718c76222a38ba3690a08cbf2959a180fb5613	refs/heads/main
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Sun Jun 29 20:07:42 2025 +0100

    atualizações página colaborador

M	UI/colaborador.php
M	js/colaborador.js

commit 048206ab83e09c5a3ce2fd6e69556958f21c0ba5	refs/heads/main
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Sun Jun 29 01:56:50 2025 +0100

    atualização pagina colaborador

A	BLL/colaborador.php
A	DAL/colaborador.php
M	UI/colaborador.php
A	js/colaborador.js

commit 6405e361fd90260ed0783866bc9c708fce3f8c5a	refs/heads/main
Author: Ines Martinho <1230806@isep.ipp.pt>
Date:   Fri Jun 27 15:28:56 2025 +0100

    pagina do colaborador

M	DAL/loginDAL.php
A	UI/colaborador.php
M	UI/footer.php
M	UI/header.php
A	UI/includes/session.php
A	UI/js/colaborador.js
M	UI/login.php
M	UI/processa_login.php
A	config/perfis.php
</pre>

</details>

#### 1231693@isep.ipp.pt
<details>
<summary>Diff</summary>

<pre>
 README.md                     |    1 
 applications.html             |   79 -------
 b/.DS_Store                   |binary
 b/DAL/Auth.php                |   58 +++++
 b/GitAnalysis/.DS_Store       |binary
 b/UI/api/dashboard.php        |   39 +++
 b/UI/api/departments.php      |   22 +
 b/UI/api/employee-reports.php |   95 ++++++++
 b/UI/api/employees.php        |  101 +++++++++
 b/UI/api/functions.php        |   22 +
 b/UI/api/guest.php            |  102 +++++++++
 b/UI/api/reports.php          |   88 +++++++
 b/UI/convidado.php            |  214 +++++++++++++++++++
 b/UI/dashboard.php            |  205 ++++++++++++++++++
 b/UI/download-logo.php        |   42 +++
 b/UI/images/logo-tlantic.png  |binary
 b/UI/js/dashboard.js          |  144 +++++++++++++
 b/UI/js/rh.js                 |  250 ++++++++++++++++++++++
 b/UI/login.php                |  198 ++++++++++++-----
 b/UI/logo-tlantic.png         |binary
 b/UI/pagina_recursos.php      |   75 ++++++
 b/UI/reports.php              |  265 ++++++++++++++++++++++++
 b/UI/rh.php                   |  180 ++++++++++++++++
 b/UI/setup-users.php          |   83 +++++++
 b/UI/style-guest.css          |  223 ++++++++++++++++++++
 b/UI/style-reports.css        |  463 ++++++++++++++++++++++++++++++++++++++++++
 b/UI/style-rh-temp.css        |  279 +++++++++++++++++++++++++
 b/UI/style-rh.css             |  279 +++++++++++++++++++++++++
 b/UI/update-temp.php          |  242 +++++++++++++++++++++
 b/api/dashboard.php           |   11 
 b/auth/conexao.php            |   12 +
 db.php                        |    6 
 index.html                    |   35 ---
 33 files changed, 3630 insertions(+), 173 deletions(-), 10 modifications(!)
</pre>
</details>
<details>
<summary>Commits</summary>

<pre>
commit e0c27eadc25d68f64a6a44a53b92acf737e916f8	refs/heads/main
Author: Catarina Cardoso <1231693@isep.ipp.pt>
Date:   Fri Jun 27 12:39:24 2025 +0100

    rh e convidado

A	UI/api/departments.php
A	UI/api/employee-reports.php
A	UI/api/employees.php
A	UI/api/functions.php
A	UI/api/guest.php
A	UI/api/reports.php
A	UI/convidado.php
A	UI/dashboard.php
A	UI/download-logo.php
A	UI/images/logo-tlantic.png
A	UI/js/dashboard.js
A	UI/js/rh.js
A	UI/logo-tlantic.png
A	UI/reports.php
A	UI/rh.php
A	UI/setup-users.php
A	UI/style-guest.css
A	UI/style-reports.css
A	UI/style-rh-temp.css
A	UI/style-rh.css
A	UI/update-temp.php
A	api/dashboard.php

commit f1e20e992ddc94895a9dc7bb47de3704d4aaa80f	refs/heads/main
Author: Catarina Cardoso <1231693@isep.ipp.pt>
Date:   Fri Jun 27 11:54:28 2025 +0100

    Pagina RH e Convidado

A	UI/api/dashboard.php

commit 97a261aa0c821b432e78fb3db1ca2019b566ab1f	refs/heads/main
Author: Catarina Cardoso <1231693@isep.ipp.pt>
Date:   Wed Jun 25 12:49:43 2025 +0100

    Adicionar Auth.php e pagina_recursos.php

A	DAL/Auth.php
A	UI/pagina_recursos.php

commit 4b52450163b6e449085dd16b5740665a1b159a88	refs/heads/main
Author: Catarina Cardoso <1231693@isep.ipp.pt>
Date:   Wed Jun 25 11:46:26 2025 +0100

    Login atualização

A	.DS_Store
A	GitAnalysis/.DS_Store
A	GitAnalysis/Sprint_1/gravardados
D	README.md
M	UI/login.php
D	applications.html
A	auth/conexao.php
D	db.php
D	index.html
</pre>

</details>

