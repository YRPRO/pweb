<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only"></span>
            <span class="icon-bar"> </span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="../index.php"><span class="glyphicon glyphicon-globe"></span> Social Book </a>
          
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
          <?php 
            if(isset($_SESSION['login'])){
              require('menuser.php');
            }
            else{ ?>
              <li class="active"><a href="../index.php"><span class="glyphicon glyphicon-home"></span> Accueil</a></li>
             

          <?php  }
          ?>
            
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
