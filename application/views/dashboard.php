<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Dashboard
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> Home</li>
        <!--<li><a href="#">Examples</a></li>
        <li class="active">Blank page</li>-->
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Dashboard</h3>
        </div>
        <div class="box-body">
            Selamat Datang <?php if ($this->session->userdata('pb_name')){ echo '<strong>'.$this->session->userdata('pb_name').'</strong>,'; }?> di SIM DIKLAT P4TK BMTI.<br>
            Saat ini anda masuk sebagai <?php echo $this->session->userdata('lvl_name'); ?>.
        </div>
        <!-- /.box-body -->
        <div class="box-footer">

        </div>
        <!-- /.box-footer-->
    </div>
    <!-- /.box -->

</section>
<!-- /.content -->