<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class FileAction extends CommonAction{
	public function do_upload()
	{
		if(intval($_REQUEST['upload_type'])==0)
		$result = $this->uploadFile();
		else
		$result = $this->uploadImage();
		if($result['status'] == 1)
		{
			$list = $result['data'];
			if(intval($_REQUEST['upload_type'])==0)
			$file_url = ".".$list[0]['recpath'].$list[0]['savename'];
			else
			$file_url = ".".$list[0]['bigrecpath'].$list[0]['savename'];
			/*$html = '<html>';
			$html.= '<head>';
			$html.= '<title>Insert Image</title>';
			$html.= '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
			$html.= '</head>';
			$html.= '<body>';
			$html.= '<script type="text/javascript">';
			$html.= 'parent.parent.KE.plugin["image"].insert("' . $_POST['id'] . '", "' . $file_url . '","' . $_POST['imgTitle'] . '","' . $_POST['imgWidth'] . '","' . $_POST['imgHeight'] . '","' . $_POST['imgBorder'] . '","' . $_POST['align'] . '");';
			$html.= '</script>';
			$html.= '</body>';
			$html.= '</html>';
			echo $html;*/
			ajax_return(array('error' => 0, 'url' => str_replace("./public/",SITE_DOMAIN.APP_ROOT."/public/",$file_url)));
		}
		else
		{
			//echo "<script>alert('".$result['info']."');</script>";
			ajax_return(array('error' => 1, 'message' => $result['info']));
		}
	}
	public function do_upload_img()
	{
		if(intval($_REQUEST['upload_type'])==0)
		$result = $this->uploadFile();
		else
		$result = $this->uploadImage();
		if($result['status'] == 1)
		{
			$list = $result['data'];
			if(intval($_REQUEST['upload_type'])==0)
			$file_url = ".".$list[0]['recpath'].$list[0]['savename'];
			else
			$file_url = ".".$list[0]['bigrecpath'].$list[0]['savename'];
			/*$html = '<html>';
			$html.= '<head>';
			$html.= '<title>Insert Image</title>';
			$html.= '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
			$html.= '</head>';
			$html.= '<body>';
			$html.= '<script type="text/javascript">';
			//$html.='alert("'.$_POST['id'].'");';
			//$html.='alert(parent.parent.document.getElementById("'.$_POST['id'].'").value);';
			//$html.='parent.parent.document.getElementById("'.$_POST['id'].'").value="'.$file_url.'";';
			$html.= 'parent.parent.KE.plugin["upload_image"].insert("' . $_POST['id'] . '", "' . $file_url . '","' . $_POST['imgTitle'] . '","' . $_POST['imgWidth'] . '","' . $_POST['imgHeight'] . '","' . $_POST['imgBorder'] . '","' . $_POST['align'] . '");';
			$html.= '</script>';
			$html.= '</body>';
			$html.= '</html>';
			echo $html;*/
			ajax_return(array('error' => 0, 'url' => str_replace("./public/",SITE_DOMAIN.APP_ROOT."/public/",$file_url)));
		}
		else
		{
			//echo "<script>alert('".$result['info']."');</script>";
			ajax_return(array('error' => 1, 'message' => $result['info']));
		}
	}

	
	public function deleteImg()
	{
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$file = $_REQUEST['file'];
		$file = explode("..",$file);
		$file = $file[4];
		$file = substr($file,1);
		@unlink(get_real_path().$file);	
	    if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']=='ES_FILE')
        {
	      	$syn_url = $GLOBALS['distribution_cfg']['OSS_DOMAIN']."/es_file.php?username=".$GLOBALS['distribution_cfg']['OSS_ACCESS_ID']."&password=".$GLOBALS['distribution_cfg']['OSS_ACCESS_KEY']."&path=".$file."&act=1";
	      	@file_get_contents($syn_url);
      	}
		save_log(l("DELETE_SUCCESS"),1);
		$this->success(l("DELETE_SUCCESS"),$ajax);
	}
}
?>