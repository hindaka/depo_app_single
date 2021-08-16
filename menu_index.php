      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p><?php echo $r1["nama"]; ?></p>
              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header text-purple">TRANSAKSI</li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-list-alt text-green"></i> <span>Input Transaksi</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="transaksi_ranap.php"><i class="fa fa-edit text-yellow"></i> <?php echo "Depo Farmasi " . $tipes[2]; ?></a></li>
                <?php
                if ($tipes[2] == 'IGD') {
                  echo '<li><a href="transaksi_rajal.php"><i class="fa fa-edit text-green"></i> Rawat Jalan</a></li>';
                }
                ?>
                <li><a href="transaksi_depo.php"><i class="fa fa-edit text-green"></i> Antar Depo</a></li>
                <!-- <li><a href="karyawan_rajal.php"><i class="fa fa-edit text-blue"></i> Karyawan <span class="badge bg-green">New</span></a></li> -->
                <!--<li><a href="karyawan.php"><i class="fa fa-edit"></i> Input Kasbon &nbsp;<span class="badge bg-green">New</span></a></li>-->
              </ul>
            </li>
            <li class="header text-purple">INVENTORY</li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-list-alt"></i> <span>Stok Obat Depo</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="stok_master.php"><i class="fa fa-circle-o text-green"></i> MASTER OBAT</a></li>
                <li><a href="stok_apbd.php"><i class="fa fa-circle-o text-blue"></i> OBAT APBD</a></li>
                <li><a href="stok_blud.php"><i class="fa fa-circle-o text-yellow"></i> OBAT BLUD</a></li>
              </ul>
            </li>
            <li><a href="find_obat.php"><i class="fa fa-circle-o text-yellow"></i> Pencarian Data Obat</a></li>
            <!-- <li class="treeview">
              <a href="#">
                <i class="fa fa-list-alt"></i> <span>Pemeliharaan Obat <span class="label label-success">New</span></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-list-alt"></i> <span>Stok Opnam</span> <i class="fa fa-angle-left pull-right"></i>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="stok_opnam_depo.php"><i class="fa fa-pencil text-blue"></i> Input Stok Opnam</a></li>
                    <li><a href="stok_opnam_depo_koreksi.php"><i class="fa fa-pencil text-blue"></i> Koreksi Stok Opname</a></li>
                    <?php
                    if ($tipes[1] == 'Admin') {
                      echo '<li><a href="stok_opnam_depo_sync.php"><i class="fa fa-pencil text-blue"></i> Sync Stok Opname</a></li>';
                    }
                    ?>
                  </ul>
                </li>
                <li><a href="#"><i class="fa fa-circle-o text-blue"></i> Cycle Counting</a></li>
              </ul>
            </li> -->
            <!-- <li class="header text-purple">LAPORAN</li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-list-alt text-blue"></i> <span>Rekap Transaksi Keluar</span><i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-list-alt text-blue"></i> <span>Obat Keluar</span><i class="fa fa-angle-left pull-right"></i>
                  </a>
                  <ul class="treeview-menu">
                    <?php if ($tipes[2] == "OK") {
                      echo '<li><a href="rekap_ranap.php"><i class="fa fa-circle-o"></i> Obat Keluar Depo Farmasi ' . $tipes[2] . '</a></li>';
                    } else {
                      echo '<li><a href="rekapkeluar.php"><i class="fa fa-circle-o"></i> Obat Keluar Rajal</a></li>
                    <li><a href="rekap_ranap.php"><i class="fa fa-circle-o"></i> Obat Keluar Depo Farmasi ' . $tipes[2] . '</a></li>';
                    } ?>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-list-alt text-blue"></i> <span>Transaksi</span><i class="fa fa-angle-left pull-right"></i>
                  </a>
                  <ul class="treeview-menu">
                    <?php if ($tipes[2] == "OK") {
                      echo '<li><a href="rekapkeluar_ranap.php"><i class="fa fa-circle-o"></i> Transaksi Keluar Depo Farmasi '.$tipes[2].'</a></li>';
                    } else {
                      echo '<li><a href="rekap.php"><i class="fa fa-circle-o"></i> Transaksi Keluar Rajal</a></li>
                      <li><a href="rekapkeluar_ranap.php"><i class="fa fa-circle-o"></i> Transaksi Keluar Depo Farmasi '.$tipes[2].'</a></li>';
                    } ?>
                  </ul>
                </li>
              </ul>
            </li>
            <li><a href="rekap_permintaan.php"><i class="fa fa-book text-blue"></i> Antar Depo <span class="label label-success">New</span></a></li> -->
            <?php
            if ($tipes[2] == "OK") {
              echo '<li class="header">DOKUMEN</li>
                <li><a href="form_cetak_paket.php"><i class="fa fa-print text-green"></i> Cetak Paket</a></li>';
            }
            ?>
            <li class="header text-purple">PENGATURAN</li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-gear text-red"></i> <span>Pengaturan</span>&nbsp;<i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="conf_penyimpanan.php"><i class="fa fa-list"></i> Lemari/Rak Obat</a></li>
                <li><a href="kadaluarsa_conf.php"><i class="fa fa-list"></i> Waktu Kontrol Kadaluarsa</a></li>
                <li><a href="pengaturan_filter_transaksi.php"><i class="fa fa-list"></i> Rentang Waktu Transaksi</a></li>
                <!-- <li><a href="tpn.php"><i class="fa fa-gear"></i> Data TPN&nbsp;<span class="badge bg-green">New</span></a></li> -->
                <li><a href="cut_off_transaksi.php"><i class="fa fa-list"></i> Cut Off Transaksi</a></li>
                <li><a href="cut_off_lemari_filter.php"><i class="fa fa-list"></i> Cut Off BY LEMARI&nbsp;<span class="badge bg-green">New</span></a></li>
                <?php
                if ($tipes[2] == 'OK') {
                  echo '<li><a href="pengaturan_paket_obat.php"><i class="fa fa-list"></i> Paket Obat/BHP OK&nbsp;<span class="badge bg-green">New</span></a></li>';
                }
                ?>
              </ul>
            </li>

            <!-- <li class="treeview">
                <a href="#">
                  <i class="fa fa-list-alt text-blue"></i> <span>Rekap Transaksi Masuk</span><i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  <li><a href="rekap_retur.php"><i class="fa fa-circle-o"></i> Rekap Retur</a></li>
                  <li><a href="rekap_mutasi_gfarmasi.php"><i class="fa fa-circle-o"></i> Mutasi Gudang Farmasi</a></li>
                  <li><a href="rekapmasuk.php"><i class="fa fa-circle-o"></i> Total Obat Masuk</a></li>
                </ul>
              </li> -->


            <!-- <li class="treeview">
              <a href="#">
                <i class="fa fa-list-alt text-red"></i> <span>Kontroling</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="kontrol_obat.php"><i class="fa fa-circle-o"></i> Pemakaian Obat</a></li>
                <li><a href="obat_kadaluarsa.php"><i class="fa fa-circle-o"></i> Obat Kadaluarsa</a></li>
                <li><a href="life_saving.php"><i class="fa fa-circle-o"></i> Obat Life Saving</a></li>
                <li><a href="ddd_calc.php"><i class="fa fa-circle-o"></i> Hitung DDD <span class="badge bg-green">New</span></a></li>
                <li><a href="cut_off_filter.php"><i class="fa fa-circle-o"></i> CUT OFF <span class="badge bg-green">New</span></a></li>
              </ul>
            </li> -->
            <li><a href="../logout.php"><i class="fa fa-lock  text-red"></i> Logout</a></li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>