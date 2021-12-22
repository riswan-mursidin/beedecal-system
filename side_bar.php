      <?php 
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
                    <li><a href="javascript: void(0);" class="has-arrow waves-effect">Pesanan Baru</a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li><a href="#">Keranjang</a></li>
                        <li><a href="#">Menunggu Konfirmasi</a></li>
                      </ul>
                    </li>
                    <li><a href="data-pesanan">Data Pesanan</a></li>
                  </ul>
                </li>
                <!-- Produksi -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-dashboard-fill"></i>
                    <span>Produksi</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <span>Proses Desain</span>
                      </a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li>
                          <a href="menunggu_designer">
                            <!-- <?php  ?> -->
                            <span class="badge rounded-pill bg-primary float-end">3</span>
                            <span>Menunggu Designer</span>
                          </a>
                        </li>
                        <li>
                          <a href="proses-desain">
                            Sedang Didesain
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li><a href="proses-cetak.html">Proses Cetak</a></li>
                    <li><a href="proses-finishing.html">Proses Finishing</a></li>
                    <li>
                      <a href="proses-pemasangan.html">Proses Pemasangan</a>
                    </li>
                  </ul>
                </li>
                <!-- Logistik -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-shopping-bag-2-fill"></i>
                    <span>Logistik</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li><a href="ambil-ditoko.html">Ambil ditoko</a></li>
                    <li><a href="pengiriman.html">Pengiriman</a></li>
                  </ul>
                </li>
                <li class="menu-title">Laporan</li>
                <!-- Laporan Order -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-bar-chart-2-fill"></i>
                    <span>Laporan Order</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li><a href="order-hari-ini.html">Order Hari ini</a></li>
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
                    <li><a href="order-hari-ini.html">Bahan Produksi</a></li>
                    <li><a href="order-selesai">Gagal Produksi</a></li>
                  </ul>
                </li>
                <li class="menu-title">Pemasangan</li>
                <!-- Laporan Pemasangan -->
                <li>
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
                </li>
                <!-- Laporan Pegawai Bebas -->
                <li class="menu-title">Relasi</li>
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
                </li>
                <!-- Relasi Toko -->
                <li>
                  <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-body-scan-fill"></i>
                    <span>Relasi Toko</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="relasi-percetakan.html">Percetakan</a>
                    </li>
                    <li>
                      <a href="relasi-penyediabahan.html">Penyedia Bahan</a>
                    </li>
                  </ul>
                </li>
                <!-- Pengaturan -->
                <li class="menu-title">Pengaturan</li>
                <!-- konfigurasi Produk -->
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
                      <a href="konfigurasiproduk-tipe">Tipe/Harga</a>
                    </li>
                    <li>
                      <a href="konfigurasiproduk-kategori">Kategori</a>
                    </li>
                    <li>
                      <a href="konfigurasiproduk-produk">Produk</a>
                    </li>
                    <li>
                      <a href="konfigurasiproduk-jenisbahan">Jenis Bahan</a>
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
                    <span>Produksi</span>
                  </a>
                  <ul class="sub-menu" aria-expanded="false">
                    <li>
                      <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <span>Proses Desain</span>
                      </a>
                      <ul class="sub-menu" aria-expanded="false">
                        <li>
                          <a href="menunggu_designer">
                            <?php  ?>
                            <span class="badge rounded-pill bg-primary float-end">3</span>
                            <span>Menunggu Designer</span>
                          </a>
                        </li>
                        <li>
                          <a href="proses-desain">
                            Sedang Didesain
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