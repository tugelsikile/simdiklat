<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Cetak</title>


    <!-- Custom styles for this template -->
    <link href="<?php echo base_url('assets/cetak.min.css');?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Tinos');
        *{
            font-size: 10pt !important; font-family: 'Tinos', serif !important;
        }
        ol li{
            margin: 0; padding: 0;
        }
        ul{
            margin-left: 20px;
        }
    </style>

</head>
<body>
<?php
if (!$data){
    echo 'tidak ada data';
} else {
    foreach ($data as $valH){
        //var_dump($valH);
        $page = array(
            0 => array(
                'file_name' => $valH->dh_page_1
            ),
            1 => array(
                'file_name' => $valH->dh_page_2
            ),
            2 => array(
                'file_name' => $valH->dh_page_3
            ),
            3 => array(
                'file_name' => $valH->dh_page_4
            ),
            4 => array(
                'file_name' => $valH->dh_page_5
            ),
            5 => array(
                'file_name' => $valH->dh_page_6
            ),
            6 => array(
                'file_name' => $valH->dh_page_7
            ),
            7 => array(
                'file_name' => $valH->dh_page_8
            )
        );
        foreach ($page as $valPage){
            if (strlen($valPage['file_name']) > 0){
                $file_path = FCPATH . 'assets/upload/'.$valH->pb_id.'/'.$valPage['file_name'];
                if (file_exists($file_path)){
                    ?>
                    <div class="page" style="padding: 0">
                        <img src="<?php echo base_url('assets/upload/'.$valH->pb_id.'/'.$valPage['file_name']);?>" width="100%">
                    </div>
                    <?php
                }
            }
        }
        ?>
        <?php
    }
}
?>

</body>
</html>