# Commits by author
#### 1230769@isep.ipp.pt
<details>
<summary>Diff</summary>

<pre>
 /DAL/database.php         |   29 ++
 /UI/login.php             |  116 ++++++++++
 /UI/processa_login.php    |   61 +++++
 DAL/Auth.php              |   58 -----
 UI/BLL/LoginBLL.php       |   56 ----
 UI/DAL/LoginDAL.php       |  121 ----------
 UI/DAL/database.php       |   28 --
 UI/acessoConvidado.html   |  380 --------------------------------
 UI/login.php              |  462 +++++++++++++++++++++++++++++++++++-!!
 UI/pagina_recursos.php    |   75 ------
 UI/processa_login.php     |   78 -!!!!
 applications.html         |   79 ------
 auth/conexao.php          |   12 -
 b/.DS_Store               |binary
 b/BLL/loginBLL.php        |   56 ++++
 b/DAL/colaboradorDAL.php  |   27 ++
 b/DAL/config.php          |   34 ++
 b/DAL/database.php        |   21 !
 b/DAL/loginDAL.php        |  121 ++++++++++
 b/UI/BLL/LoginBLL.php     |   56 ++++
 b/UI/DAL/LoginDAL.php     |  121 ++++++++++
 b/UI/DAL/database.php     |   28 ++
 b/UI/acessoConvidado.html |  380 ++++++++++++++++++++++++++++++++
 b/UI/footer.php           |    3 
 b/UI/header.php           |   39 +++
 b/UI/index.php            |    5 
 b/UI/login.html           |  533 ++++++++++++++++++++++++++++++++++++++++++++++
 b/UI/login.php            |  432 +++++++++++++++++++++++++++++++++++-
 b/UI/logout.php           |   14 +
 b/UI/processa_login.php   |   47 -!!!
 b/UI/processa_registo.php |   63 +++++
 b/UI/recuperar_senha.php  |   96 ++++++++
 b/UI/registo.html         |  411 +++++++++++++++++++++++++++++++++++
 b/UI/style.css            |   36 +++
 b/index.php               |   66 ++++!
 b/script.js               |   97 ++++++++
 db.php                    |    6 
 login.html                |  487 ------------------------------------------
 38 files changed, 3217 insertions(+), 1349 deletions(-), 168 modifications(!)
</pre>
</details>
<details>
<summary>Commits</summary>

<pre>
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
 0 files changed
</pre>
</details>
<details>
<summary>Commits</summary>

<pre>
</pre>

</details>

#### 1231693@isep.ipp.pt
<details>
<summary>Diff</summary>

<pre>
 README.md                |    1 
 applications.html        |   79 ------------------
 b/.DS_Store              |binary
 b/DAL/Auth.php           |   58 +++++++++++++
 b/GitAnalysis/.DS_Store  |binary
 b/UI/login.php           |  198 ++++++++++++++++++++++++++++++++------------!!
 b/UI/pagina_recursos.php |   75 +++++++++++++++++
 b/auth/conexao.php       |   12 ++
 db.php                   |    6 -
 index.html               |   35 --------
 10 files changed, 281 insertions(+), 173 deletions(-), 10 modifications(!)
</pre>
</details>
<details>
<summary>Commits</summary>

<pre>
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

