<?php
/*
Plugin Name: my-postie-addon
Plugin URI: https://github.com/iliu-net/my-postie-addon
Description: My Postie customisation add-on
Version: 1.0
Author: Alejandro Liu
Author URI: http://0ink.net
License: MIT
*/
/*
Copyright (c) 2017 Alejandro Liu

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

if (!class_exists('MyPostieAddOn')) {
  class MyPostieAddOn {
    public function __construct() {
      // Register filters
      add_filter('postie_post_before', [$this,'post_modifier'],10,2);
    }
    static public function fmt_postie_addr($n) {
      $html = '';
      if (is_array($n)) {
	$html .= '<a href="'.esc_html($n['mailbox'].'@'.$n['host']).'"';
	if ($n['personal']) {
	  $html .= ' title="'.esc_html($n['mailbox'].'@'.$n['host']).'">'.esc_html($n['personal']);
	} else {
	  $html .= '>'.esc_html($n['mailbox'].'@'.$n['host']);
	}
	$html .= '</a>';
      } else {
	$html .= '<a href="mailto:'.esc_html($n).'">'.esc_html($n).'</a>';
      }
      return $html;
    }
    public function post_modifier($post, $headers) {
      $prefix = '';
      if ($post['email_author']) {
	$prefix .= '<tr><th>From:</th><td>'.self::fmt_postie_addr($post['email_author']).'</td></tr>';
      }
      if ($headers['to']) {
	$prefix .= '<tr><th>To:</th><td>';
	foreach ($headers['to'] as $n) {
	  $prefix .= self::fmt_postie_addr($n).'<br/>';
	}
      }
      if ($prefix) {
	$post['post_content'] = '<div><table>'.$prefix.'</table></div>'.$post['post_content'];
      }
      if (FALSE) { //(count($headers)) {
	$post['post_content'] .= '<div><table>';
	foreach ($headers as $i=>$j) {
	  switch ($i) {
	    case 'delivered-to':
	      $post['post_content'] .= '<tr><th>'.$i.'</th><td>'.self::fmt_postie_addr($j).'</td></tr>';
	      break;
	    case 'sender':
	    case 'reply-to':
	    case 'from':
	      $post['post_content'] .= '<tr><th>'.$i.'</th><td>'.self::fmt_postie_addr($j).'</td></tr>';
	      break;
	    case 'to':
	    case 'cc':
	    case 'bcc':
	      $tmp = [];
	      foreach ($j as $n) {
		$tmp[] = self::fmt_postie_addr($n);
	      }
	      $post['post_content'] .= '<tr><th>'.$i.'</th><td>'.implode('<br/>',$tmp).'</td></tr>';
	      break;
	    default:
	      if (is_array($j)) {
		$post['post_content'] .= '<tr><th>'.$i.'</th><td>'.implode('<br/>',$j).'</td></tr>';
		
	      } else {
		$post['post_content'] .= '<tr><th>'.$i.'</th><td>'.$j.'</td></tr>';
	      }
	  }
	}
	$post['post_content'] .= '</table></div>';
	
      }
      return $post;
    }
  }
}
if (class_exists('MyPostieAddOn')) {
  $auto_content = new MyPostieAddOn();
}

