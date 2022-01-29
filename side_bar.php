      <?php 

      // menunggu desain
      $desain_menunggu = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Menunggu Designer");
      $jumds = mysqli_num_rows($desain_menunggu);

      // proses desain
      $proses_desain = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Proses Desain");
      $jumps = mysqli_num_rows($proses_desain);

      // siap cetak
      $querycet = "SELECT * FROM data_pemesanan WHERE id_owner='$id' AND status_order='Siap Cetak' OR status_order='Cetak Ulang'";
      $siapp_cetak = mysqli_query($db->conn, $querycet);
      $jumsc = mysqli_num_rows($siapp_cetak);

      // proses cetak
      $prosess_cetak = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Proses Cetak");
      $jumpc = mysqli_num_rows($prosess_cetak);

      // menunggu finishing
      $menunggu_finishing = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Menunggu Finishing");
      $jummf = mysqli_num_rows($menunggu_finishing);

      // proses finishing
      $proses_finishing = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Proses Finishing");
      $jumpf = mysqli_num_rows($proses_finishing);

      // siap pasang
      $siap_pasang = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Siap Dipasang");
      $jumsp = mysqli_num_rows($siap_pasang);

      // proses pasang
      $proses_pasangg = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Proses Pemasangan");
      $jumpp = mysqli_num_rows($proses_pasangg);

      // logistik ambil di toko
      $jumlogistik_toko = 0;
      $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_pengiriman_order","Tidak");
      while($roworder=mysqli_fetch_assoc($order)){
        if($roworder['status_order'] == "Selesai Finishing" || $roworder['status_order'] == "Selesai Dicetak" || $roworder['status_order'] == "Selesai Dipasang" || $roworder['status_order'] == "Menunggu Finishing" || $roworder['status_order'] == "Siap Dipasang"){
          ++$jumlogistik_toko;
        }
      }

      // logistik pengiriman
      $jumlogistik_kirim = 0;
      $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_pengiriman_order","Ya");
      while($roworder=mysqli_fetch_assoc($order)){
        if($roworder['status_order'] == "Selesai Finishing" || $roworder['status_order'] == "Menunggu Finishing" || $roworder['status_order'] == "Selesai Dicetak"){
          ++$jumlogistik_kirim;
        }
      }

      $role = $row['level_user'];
      if($role == "Owner" || $role == "Admin"){
      ?>
        <div class="vertical-menu">
          <div data-simplebar class="h-100">
            <!--- Sidemenu -->
            <div id="sidebar-menu">
              <!-- Left Menu Start -->
              <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>
  
                <li>
                  <a href="index" class="waves-effect">
                    <i class="mdi mdi-home-variant-outline"></i>
                    <span>Dashboard</span>
                  </a>
                </li>
                <li class="menu-title">Pemesanan</li>
                <!-- pemesanan -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-shopping-cart-fill"></i>
                    <span>Pemesanan</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <!-- <li><a href="javascript: void(0);" class="has-arrow waves-effect">Pesanan Baru</a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li><a href="#">Keranjang</a></li>
                        <li><a href="#">Menunggu Konfirmasi</a></li>
                      </ul>
                    </li> -->
                    <li><a href="data-pesanan">Data Pesanan</a></li>
                  </ul>
                </li>
                <!-- Produksi -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-dashboard-fill"></i>
                      <?php if($jumds + $jumps + $jumsc + $jumpc + $jummf + $jumpf + $jumsp + $jumpp != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumds + $jumps + $jumsc + $jumpc + $jummf + $jumpf + $jumsp + $jumpp ?></span>
                      <?php } ?>
                    <span>Produksi</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="javascript: void(0);" class="has-arrow waves-effect">
                      <?php if($jumds + $jumps != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumds + $jumps ?></span>
                      <?php } ?>
                        <span>Proses Desain</span>
                      </a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li>
                          <a href="menunggu_designer">
                            <?php if($jumds != 0){ ?>
                              <span class="badge rounded-pill bg-danger float-end"><?= $jumds ?></span>
                            <?php } ?>
                            <span>Menunggu Designer</span>
                          </a>
                        </li>
                        <li>
                          <a href="proses-desain">
                          <?php if( $jumps != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?=  $jumps ?></span>
                          <?php } ?>
                            <span>Sedang Didesain</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li>
                      <a href="javascript: void(0);" class="has-arrow waves-effect">
                      <?php if($jumsc + $jumpc != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumsc + $jumpc ?></span>
                      <?php } ?>
                        <span>Proses Cetak</span>
                      </a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li>
                          <a href="siap-cetak">
                          <?php if($jumsc != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jumsc ?></span>
                          <?php } ?>
                            <span>Siap Cetak</span>
                          </a>
                        </li>
                        <li>
                          <a href="sedang-dicetak">
                          <?php if($jumpc != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jumpc ?></span>
                          <?php } ?>
                            <span>Sedang Dicetak</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li>
                      <a href="javascript: void(0);" class="has-arrow waves-effect">
                      <?php if($jummf + $jumpf != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jummf + $jumpf ?></span>
                      <?php } ?>
                        <span>Proses Finishing</span>
                      </a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li>
                          <a href="menunggu-finishing">
                          <?php if($jummf != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jummf ?></span>
                          <?php } ?>
                            <span>Menunggu Finishing</span>
                          </a>
                        </li>
                        <li>
                          <a href="proses-finishing">
                          <?php if($jumpf != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jumpf ?></span>
                          <?php } ?>
                            <span>Finishing Berjalan</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li>
                      <a href="javascript: void(0);" class="has-arrow waves-effect">
                      <?php if($jumsp + $jumpp != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumsp + $jumpp ?></span>
                      <?php } ?>
                        <span>Proses Pemasangan</span>
                      </a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li>
                          <a href="siap-dipasang">
                          <?php if($jumsp != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jumsp ?></span>
                          <?php } ?>
                            <span>Siap Dipasang</span>
                          </a>
                        </li>
                        <li>
                          <a href="proses-pasang">
                          <?php if($jumpp != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jumpp ?></span>
                          <?php } ?>
                            <span>Sedang Dipasang</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </li>
                <!-- Logistik -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-shopping-bag-2-fill"></i>
                      <?php if($jumlogistik_toko + $jumlogistik_kirim != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumlogistik_toko + $jumlogistik_kirim ?></span>
                      <?php } ?>
                    <span>Logistik</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="ambil-ditoko">
                      <?php if($jumlogistik_toko != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumlogistik_toko ?></span>
                      <?php } ?>
                        Ambil ditoko
                      </a>
                    </li>
                    <li>
                      <a href="pengiriman">
                      <?php if($jumlogistik_kirim != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumlogistik_kirim ?></span>
                      <?php } ?>
                        Pengiriman
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="menu-title">Laporan</li>
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class=" ri-coins-line"></i>
                    <span>Laporan Biaya</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li><a href="biaya-pengeluaran">Biaya Pengeluaran</a></li>
                    <li><a href="kategori-pengeluaran">Kategori Pengeluaran</a></li>
                  </ul>
                </li>
                <!-- Laporan Order -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-bar-chart-2-fill"></i>
                    <span>Laporan Order</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li><a href="order-hari-ini">Order Hari ini</a></li>
                    <li><a href="order-selesai">Order Selesai</a></li>
                    <li><a href="data-grafik">Data Grafik</a></li>
                  </ul>
                </li>
                <!-- Laporan Bahan -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-pie-chart-fill"></i>
                    <span>Laporan Bahan</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li><a href="bahan-produksi">Bahan Produksi</a></li>
                    <!-- <li><a href="#">Gagal Produksi</a></li> -->
                  </ul>
                </li>
                <li class="menu-title">Pemasangan</li>
                <!-- Laporan Pemasangan -->
                <!-- <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-brush-4-fill"></i>
                    <span>Data Pemasangan</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="pendapatan-pemasangan.html">Pendapatan</a>
                    </li>
                    <li>
                      <a href="pengaturan-pemasangan.html">Pengaturan</a>
                    </li>
                  </ul>
                </li> -->
                <!-- Laporan Pegawai Bebas -->
                <!-- <li class="menu-title">Relasi</li>
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-team-fill"></i>
                    <span>Pegawai Lepas</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="pegawailepas-pemasang.html">Pendapatan</a>
                    </li>
                    <li>
                      <a href="pegawailepas-designer.html">Designer</a>
                    </li>
                    <li>
                      <a href="pegawailepas-kurir.html">Kurir</a>
                    </li>
                    <li>
                      <a href="pegawailepas-agen.html">Agen</a>
                    </li>
                  </ul>
                </li> -->
                <!-- Relasi Toko -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-body-scan-fill"></i>
                    <span>Relasi Toko</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="relasi-percetakan">Percetakan</a>
                    </li>
                    <!-- <li>
                      <a href="relasi-penyediabahan.html">Penyedia Bahan</a>
                    </li> -->
                  </ul>
                </li>
                <!-- Pengaturan -->
                <li class="menu-title">Pengaturan</li>
                <!-- konfigurasi Produk -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="dripicons-inbox"></i>
                    <span>Produk</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="konfigurasiproduk-tipe">Custom</a>
                    </li>
                    <li>
                    <li>
                      <a href="konfigurasiproduk-produk">Retail</a>
                    </li>
                  </ul>
                </li>
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-file-settings-fill"></i>
                    <span>Konfigurasi Produk</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="konfigurasiproduk-merek">Merek Kendaraan</a>
                    </li>
                    <li>
                      <a href="konfigurasiproduk-kategori">Kategori Produk</a>
                    </li>
                    <li>
                    <li>
                      <a href="konfigurasiproduk-jenisbahan">Jenis Bahan</a>
                    </li>
                    <li>
                      <a href="konfigurasiproduk-laminatingbahan">Bahan Laminating</a>
                    </li>
                    <li>
                      <a href="konfigurasiproduk-ukuranbahan">Ukuran Bahan</a>
                    </li>
                  </ul>
                </li>
                <!-- Konfigurasi Toko -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-user-settings-fill"></i>
                    <span>Konfigurasi Toko</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="konfigurasi-profiltoko">Profil Toko</a>
                    </li>
                    <li>
                      <a href="konfigurasi-rek">Rekening Toko</a>
                    </li>
                    <li>
                      <a href="konfigurasi-pegawaitoko">Pegawai Toko</a>
                    </li>
                    <li>
                      <a href="konfigurasi-pelanggantoko">Pelanggan Toko</a>
                    </li>
                    <li>
                      <a href="sumber-pesanan">Sumber</a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
            <!-- Sidebar -->
          </div>
        </div>
      <?php }elseif($role == "Desainer"){ ?>
        <div class="vertical-menu">
          <div data-simplebar class="h-100">
            <!--- Sidemenu -->
            <div id="sidebar-menu">
              <!-- Left Menu Start -->
              <ul class="metismenu list-unstyled" id="side-menu">
                <!-- Produksi -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-dashboard-fill"></i>
                      <?php if($jumds + $jumps != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumds + $jumps ?></span>
                      <?php } ?>
                    <span>Produksi</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="javascript: void(0);" class="has-arrow waves-effect">
                      <?php if($jumds + $jumps != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumds + $jumps ?></span>
                      <?php } ?>
                        <span>Proses Desain</span>
                      </a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li>
                          <a href="menunggu_designer">
                            <?php if($jumds != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jumds ?></span>
                            <?php } ?>
                            <span>Menunggu Designer</span>
                          </a>
                        </li>
                        <li>
                          <a href="proses-desain">
                          <?php if($jumps != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jumps ?></span>
                            <?php } ?>
                            <span>Sedang Didesain</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
            <!-- Sidebar -->
          </div>
        </div>
      <?php }elseif($role == "Produksi"){ ?>
        <div class="vertical-menu">
          <div data-simplebar class="h-100">
            <!--- Sidemenu -->
            <div id="sidebar-menu">
              <!-- Left Menu Start -->
              <ul class="metismenu list-unstyled" id="side-menu">
                <!-- Produksi -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-dashboard-fill"></i>
                    <?php if($jumsc + $jumpc + $jummf + $jumpf != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumsc + $jumpc + $jummf + $jumpf ?></span>
                      <?php } ?>
                    <span>Produksi</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="javascript: void(0);" class="has-arrow waves-effect">
                      <?php if($jumsc + $jumpc != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumsc + $jumpc ?></span>
                      <?php } ?>
                        <span>Proses Cetak</span>
                      </a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li>
                          <a href="siap-cetak">
                          <?php if($jumsc != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jumsc ?></span>
                          <?php } ?>
                            <span>Siap Cetak</span>
                          </a>
                        </li>
                        <li>
                          <a href="sedang-dicetak">
                          <?php if($jumpc != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jumpc ?></span>
                          <?php } ?>
                            <span>Sedang Dicetak</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li>
                      <a href="javascript: void(0);" class="has-arrow waves-effect">
                      <?php if($jummf + $jumpf != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jummf + $jumpf ?></span>
                      <?php } ?>
                        <span>Proses Finishing</span>
                      </a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li>
                          <a href="menunggu-finishing">
                          <?php if($jummf != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jummf ?></span>
                          <?php } ?>
                            <span>Menunggu Finishing</span>
                          </a>
                        </li>
                        <li>
                        <?php if($jumpf != 0){ ?>
                          <span class="badge rounded-pill bg-danger float-end"><?= $jumpf ?></span>
                        <?php } ?>
                          <a href="proses-finishing">
                            <span>Finishing Berjalan</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
            <!-- Sidebar -->
          </div>
        </div>
        <?php }elseif($role == "Pemasang"){ ?>
          <div class="vertical-menu">
          <div data-simplebar class="h-100">
            <!--- Sidemenu -->
            <div id="sidebar-menu">
              <!-- Left Menu Start -->
              <ul class="metismenu list-unstyled" id="side-menu">
                <!-- Produksi -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-dashboard-fill"></i>
                      <?php if($jumsp + $jumpp != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumsp + $jumpp ?></span>
                      <?php } ?>
                    <span>Produksi</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="javascript: void(0);" class="has-arrow waves-effect">
                      <?php if($jumsp + $jumpp != 0){ ?>
                        <span class="badge rounded-pill bg-danger float-end"><?= $jumsp + $jumpp ?></span>
                      <?php } ?>
                        <span>Proses Pemasangan</span>
                      </a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li>
                          <a href="siap-dipasang">
                          <?php if($jumsp != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?= $jumsp ?></span>
                          <?php } ?>
                            <span>Siap Dipasang</span>
                          </a>
                        </li>
                        <li>
                          <a href="proses-pasang">
                          <?php if( $jumpp != 0){ ?>
                            <span class="badge rounded-pill bg-danger float-end"><?=  $jumpp ?></span>
                          <?php } ?>
                            <span>Sedang Dipasang</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
            <!-- Sidebar -->
          </div>
        </div>
        <?php } ?>