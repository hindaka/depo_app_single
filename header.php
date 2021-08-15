<?php
$total_notif=0;
?>
<header class="main-header">
    <a href="index.php" class="logo"><b>SIMRS</b> <?php echo $version_depo; ?></a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <?php if($total_notif>0){ ?>
            <li class="dropdown notifications-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-bell-o"></i>
                <span class="label label-warning"><?php echo $total_notif; ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="header">Ada <?php echo $total_notif; ?> Pemberitahuan</li>
                <li>
                  <!-- inner menu: contains the actual data -->
                  <ul class="menu">
                    <!-- <li>
                      <a href="#">
                        <i class="fa fa-info text-aqua"></i> {{0}} Obat Baru Bulan ini
                      </a>
                    </li> -->
                    <li>
                      <a href="obat_kadaluarsa.php">
                        <i class="fa fa-warning text-yellow"></i> <?php echo $data_kadaluarsa['total_kadaluarsa']; ?> Obat Mendekati kadaluarsa
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
          <?php }else{ ?>

          <?php } ?>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="../dist/img/user2-160x160.jpg" class="user-image" alt="User Image"/>
              <span class="hidden-xs"><?php echo $r1["nama"]; ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
                <p>
                  <?php echo $r1["nama"]; ?> - <?php echo $r1["tipe"]; ?>
                  <small>Member sejak <?php echo $r1["tanggal"]; ?></small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="change_password.php" class="btn btn-primary btn-flat">Change Password</a>
                </div>
                <div class="pull-right">
                  <a href="../logout.php" class="btn btn-danger btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header><!-- ./static header -->
