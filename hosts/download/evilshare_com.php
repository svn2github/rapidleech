<?php
if (! defined ( 'RAPIDLEECH' ))
{
	require_once ("index.html");
	exit ();
}

class evilshare_com extends DownloadClass 
{
	public function Download($link) 
	{
		global $premium_acc;
		
		if ( isset($_POST['step'] ) ) 
		{
			if ( $_POST['step'] == 1 ) 
			{
				return $this->DownloadFree($link);
			} 
			else 
			{
				return $this->EnterCaptchaCode($link);
			}
		} 
		else 
		{
			return $this->EnterCaptchaCode($link);
		}
	}
	
	private function EnterCaptchaCode($link)
	{
		global $nn, $PHP_SELF, $pauth;
		$page = $this->GetPage($link);
		
		//is_present ( $page, "Due to a violation of our terms of use, the file has been removed from the server." );

		$cookie = "";
		preg_match_all("/Set-Cookie: ([^;]+;)/", $page, $cook);
		$arraySize = count($cook);

		for ( $i=0;$i<$arraySize;$i++)
		{
			$cookie=$cookie.array_shift($cook[1]);
		}
		
		$count = trim ( cut_str ( $page, '<span id="countdown">', '</span>' ) );
		
		$op = trim ( cut_str ( $page, '<input type="hidden" name="op" value="', '"' ) );
		$id = trim ( cut_str ( $page, '<input type="hidden" name="id" value="', '"' ) );
		$rand = trim ( cut_str ( $page, '<input type="hidden" name="rand" value="', '"' ) );
		$referer = trim ( cut_str ( $page, '<input type="hidden" name="referer" value="', '"' ) );
		$method_free = trim ( cut_str ( $page, '<input type="hidden" name="method_free" value="', '"' ) );
		$method_premium = trim ( cut_str ( $page, '<input type="hidden" name="method_premium" value="', '"' ) );
		$down_script = trim ( cut_str ( $page, '<input type="hidden" name="down_script" value="', '"' ) );
		
		$captchaImage = trim ( cut_str ( $page, '<img src="http://evilshare.com/captchas/', '">' ) );
		$captcha_access_url = "http://evilshare.com/captchas/".$captchaImage;
		
		insert_timer( $count, "Waiting link timelock");
		
		print "<form name=\"dl\" action=\"$PHP_SELF\" method=\"post\">\n";
		print "<input type=\"hidden\" name=\"link\" value=\"" . urlencode ( $link ) . "\">\n";
		
		print "<input type=\"hidden\" name=\"op\" value=\"" . urlencode ( $op ) . "\">\n";
		print "<input type=\"hidden\" name=\"id\" value=\"" . urlencode ( $id ) . "\">\n";
		print "<input type=\"hidden\" name=\"rand\" value=\"" . urlencode ( $rand ) . "\">\n";
		print "<input type=\"hidden\" name=\"referer\" value=\"" . urlencode ( $referer ) . "\">\n";
		print "<input type=\"hidden\" name=\"method_free\" value=\"" . urlencode ( $method_free ) . "\">\n";
		print "<input type=\"hidden\" name=\"method_premium\" value=\"" . urlencode ( $method_premium ) . "\">\n";
		print "<input type=\"hidden\" name=\"down_script\" value=\"" . urlencode ( $down_script ) . "\">\n";
		print "<input type=\"hidden\" name=\"step\" value=\"1\">\n";
		
		print "<input type=\"hidden\" name=\"comment\" id=\"comment\" value=\"" . $_GET ["comment"] . "\">\n";
		print "<input type=\"hidden\" name=\"email\" id=\"email\" value=\"" . $_GET ["email"] . "\">\n";
		print "<input type=\"hidden\" name=\"partSize\" id=\"partSize\" value=\"" . $_GET ["partSize"] . "\">\n";
		print "<input type=\"hidden\" name=\"method\" id=\"method\" value=\"" . $_GET ["method"] . "\">\n";
		print "<input type=\"hidden\" name=\"proxy\" id=\"proxy\" value=\"" . $_GET ["proxy"] . "\">\n";
		print "<input type=\"hidden\" name=\"proxyuser\" id=\"proxyuser\" value=\"" . $_GET ["proxyuser"] . "\">\n";
		print "<input type=\"hidden\" name=\"proxypass\" id=\"proxypass\" value=\"" . $_GET ["proxypass"] . "\">\n";
		print "<input type=\"hidden\" name=\"path\" id=\"path\" value=\"" . $_GET ["path"] . "\">\n";
		print "<h4>".lang(301)." <img src=\"$captcha_access_url\" > ".lang(302).": ";
		print "<input type=\"text\" name=\"code\" size=\"4\">&nbsp;&nbsp;";
		print "<input type=\"submit\" onclick=\"return check()\" value=\"".lang(303)."\"></h4>\n";
		
		print "<script language=\"JavaScript\">" . $nn . "function check() {" . $nn . "var imagecode=document.dl.code.value;" . $nn . 'if (imagecode == "") { window.alert("You didn\'t enter the image verification code"); return false; }' . $nn . 'else { return true; }' . $nn . '}' . $nn . '</script>' . $nn;
		print "</form>\n</body>\n</html>";
	}	
		
	private function DownloadFree($link)
	{
		global $Referer;
				
		if ( $_GET ["step"] == "1" ) 
		{
			$post = array ();
			$post ["op"] = $_GET ["op"];
			$post ["id"] = $_GET ["id"];
			$post ["rand"] = $_GET ["rand"];
			$post ["referer"] = $_GET ["referer"];
			$post ["method_free"] = $_GET ["method_free"];
			$post ["method_premium"] = $_GET ["method_premium"];
			$post ["code"] = $_GET ["code"];
			$post ["down_script"] = $_GET ["down_script"];			
		} else
		{
			// error
			html_error ( "Kindly execute catpcha step then this step come.", 0 );
		}
		
		$page = $this->GetPage($link, 0, $post, $Referer );
		preg_match ( '/Location: (.*)/', $page, $newredir );
		
		$FileName = "";		
		$Href = trim ( $newredir [1] );
		$Url = parse_url ( $Href );
		$FileName = ! $FileName ? basename ( $Url ["path"] ) : $FileName;
		
		//$this->RedirectDownload($Href,$FileName,$cookie, 0,$Referer);
		$this->RedirectDownload( $Href, $FileName, 0, 0, $Referer );
		exit ();
	}
}	
// download plug-in writted by rajmalhotra  12 Dec 2009	
// Updated by rajmalhotra on 14 Dec 09 for trim the Href		
?>