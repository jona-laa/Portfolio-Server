# RESTful API - Portfolio
RESTful API for my [Portfolio Webpage](https://github.com/jona-laa/Portfolio-Client). 
Serves data about projects, jobs, studies, skillset, and a small abuout section.

### Endpoints
#### Get
* http://studenter.miun.se/~jola1803/dt173g/portfolio/api/bio.php
* http://studenter.miun.se/~jola1803/dt173g/portfolio/api/skills.php
* http://studenter.miun.se/~jola1803/dt173g/portfolio/api/jobs.php
* http://studenter.miun.se/~jola1803/dt173g/portfolio/api/courses.php
* http://studenter.miun.se/~jola1803/dt173g/portfolio/api/projects.php
#### Get One
* http://studenter.miun.se/~jola1803/dt173g/portfolio/api/bio.php?id=
* http://studenter.miun.se/~jola1803/dt173g/portfolio/api/skills.php?id=
* http://studenter.miun.se/~jola1803/dt173g/portfolio/api/jobs.php?id=
* http://studenter.miun.se/~jola1803/dt173g/portfolio/api/courses.php?id=
* http://studenter.miun.se/~jola1803/dt173g/portfolio/api/projects.php?id=
#### Post
* Implemented. Requires access token
#### PUT
* Implemented. Requires access token
#### Delete
* Implemented. Requires access token


## Usage
* `git clone https://github.com/jona-laa/Portfolio-Server.git`
* Move folder to an AMP-stack environment. 
  * Usually available at http://localhost:8080/portfolio/api/{endpoint name} 
* You will also have to create a database, and a config.php file with credentials etc.
