<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar sidebar-left">
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="<?php if (isset($dashboard)){ echo 'active'; }?> dashboard">
            <a data-target="dashboard" href="<?php echo base_url('');?>" onclick="load_page(this);return false">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            </a>
        </li>
        <?php
        if ($this->session->userdata('lvl_diklat') > 0){
            ?>
            <li class="<?php if (isset($diklat)){ echo 'active'; }?> treeview diklat">
                <a href="#">
                    <i class="fa fa-briefcase"></i>
                    <span>Diklat</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a data-target="diklat" onclick="load_page(this);return false" href="<?php echo base_url('diklat');?>"><i class="fa fa-briefcase"></i> Diklat</a></li>
                    <li><a data-target="diklat" onclick="load_page(this);return false" href="<?php echo base_url('diklat/pb');?>"><i class="fa fa-building"></i> Pusat Belajar</a></li>
                </ul>
            </li>
            <?php
        }
        if ($this->session->userdata('lvl_diklat_kelas') == 1 && $this->session->userdata('pb_id')){
            ?>
            <li class="<?php if (isset($kelas)){ echo 'active'; }?> kelas">
                <a href="<?php echo base_url('kelas');?>" data-target="kelas" onclick="load_page(this);return false">
                    <i class="fa fa-mortar-board"></i> <span>Kelas Diklat</span>
                </a>
            </li>
            <?php
        }
        if ($this->session->userdata('lvl_soal') > 0){
            ?>
            <li class="<?php if (isset($soal)){ echo 'active'; }?> treeview soal">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Paket Soal</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a data-target="soal" onclick="load_page(this);return false" href="<?php echo base_url('soal/paket');?>"><i class="fa fa-bookmark"></i> Paket Soal</a></li>
                    <li><a data-target="soal" onclick="load_page(this);return false" href="<?php echo base_url('soal/data_soal');?>"><i class="fa fa-bookmark-o"></i> Soal</a></li>
                </ul>
            </li>
            <?php
        }
        if ($this->session->userdata('lvl_diklat_nilai') > 0){
            ?>
            <li class="<?php if (isset($nilai)){ echo 'active'; }?> treeview nilai">
                <a href="#">
                    <i class="fa fa-bar-chart"></i>
                    <span>Nilai</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a data-target="nilai" onclick="load_page(this);return false" href="<?php echo base_url('nilai/sikap');?>"><i class="fa fa-circle-o"></i> Nilai Sikap</a></li>
                    <li><a data-target="nilai" onclick="load_page(this);return false" href="<?php echo base_url('nilai/keterampilan');?>"><i class="fa fa-circle-o"></i> Nilai Keterampilan</a></li>
                    <li><a data-target="nilai" onclick="load_page(this);return false" href="<?php echo base_url('nilai/post_test');?>"><i class="fa fa-circle-o"></i> Post Test</a></li>
                    <li><a data-target="nilai" onclick="load_page(this);return false" href="<?php echo base_url('nilai/pre_test');?>"><i class="fa fa-circle-o"></i> Pre Test</a></li>
                    <li><a data-target="nilai" onclick="load_page(this);return false" href="<?php echo base_url('nilai/nilai_akhir');?>"><i class="fa fa-circle-o"></i> Nilai Akhir</a></li>
                </ul>
            </li>
            <?php
        }
        if ($this->session->userdata('lvl_user') > 0){
            ?>
            <li class="<?php if (isset($user)){ echo 'active'; }?> user">
                <a href="<?php echo base_url('user');?>" data-target="user" onclick="load_page(this);return false">
                    <i class="fa fa-users"></i> <span>Pengguna</span>
                </a>
            </li>
            <?php
        }
        ?>
        <li class="">
            <a href="<?php echo base_url('logout');?>">
                <i class="fa fa-sign-out"></i> <span>Keluar</span>
            </a>
        </li>
    </ul>
</section>
<!-- /.sidebar -->