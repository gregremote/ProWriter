<?php 


add_shortcode('gallery', 'spro_tiler');

function get_category_id($cat_name){
	$term = get_term_by('name', $cat_name, 'category');
	return $term->term_id;
}

function spro_tiler( $atts ) {
	
	global $post;
	

	extract( shortcode_atts( array(
		'tiles' => 3,
		'posts'=>16,
		'cat'=>'Uncategorized',
		'height' => 0,
		'excerpt' => 'yes',
		'more_text' => 'more',
		'class' => ''
	), $atts ) );


	$catid=get_category_id($cat);
	
	
	//query_posts('cat='.$catid.'&numposts='.$posts);
	$tiler_loop = new WP_Query('cat='.$catid.'&numberposts='.$posts.'&posts_per_page='.$posts);

	$i=1;
	
	if($tiles==2){
			$scode='one_half';
			if($height==0)
				$height=300;
		}
		
 		if($tiles==3){
			$scode='one_third';
			if($height==0)
				$height=256;
		}
		
		if($tiles==4){
			$scode='one_fourth';
			if($height==0)
				$height=200;
		}
		if($tiles==5){
			$scode='one_fifth';
			if($height==0)
				$height=150;
		}
		$output="";
		
		
		
		if( $tiler_loop->have_posts() ):
   		 while( $tiler_loop->have_posts() ): $tiler_loop->the_post();
		 
     		$link=get_permalink();

			if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
			
				
				$spro_gallery_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'tile-thumb' );
				
				}
				else {
					
					break;
					
				}
		
		
			$last="";
			
			if($i==$tiles){
				$last=" last";
				$i=0;
			}
			
			$i++;
		
				
			
			$output=$output.'<div class="'.$class." ".$scode.$last.'" id="tiler'.get_the_id().'"><div class="fl-tiler-container">
			<div class="fl-tile">
			<div class="fl-tile-top front">
        	<a href="'.$link.'"><img src="'.$spro_gallery_img[0].'"';
			if($tiles<=3) $output=$output.' style="width:100%; height: auto;"';
			else $output=$output.' style="height:100%; width: auto;"';
			$output=$output.' /></a>
			</div>
			<div class="fl-tile-bottom back">
				<a href="'.$link.'"><h3 class="spro-tiler-title">'.get_the_title().'</h3></a>
				<p class="spro-tiler-excerpt">'.get_the_excerpt().'</p>
			</div>
			
		</div>
						
			
			</div>
			</div>';
		
		 endwhile;
		
		
		$output=$output.'<div style="clear:both"> </div>';
		
		else : $output="";
		
		 endif; 
		wp_reset_postdata(); //$output2=test;
            	
	// Now we are returning the HTML code back to the place from where the shortcode was called.
	return ($output);
}



// end socioal media

add_shortcode('blog_widget', 'spro_blog');

function string_limit_words($string, $word_limit)
{
  $words = explode(' ', $string, ($word_limit + 1));
  if(count($words) > $word_limit)
  array_pop($words);
  return implode(' ', $words);
}


function spro_blog( $atts ) {
	global $post;
	

	extract( shortcode_atts( array(
		'image' => 'yes',
		'date' => 'no',
		'posts'=>5,
		'cat'=>'Uncategorized',
		'size' =>100,
		'length' =>0
	), $atts ) );

	$catid=get_category_id($cat);
	
	
	//query_posts('cat='.$catid.'&posts_per_page='.$posts);
	
	$blogger_loop = new WP_Query('cat='.$catid.'&posts_per_page='.$posts);

	
	$output='<ul class="media-list" >';
	$media="";
			
	if( $blogger_loop->have_posts() ):
    	while( $blogger_loop->have_posts() ): $blogger_loop->the_post();
      
		
			if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
				$spro_gallery_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );}
		
		
	
		
		
		
		if($date=="yes"){
			$media="";
			$spro_date=strtoupper(get_the_time( 'M j')); 
			$spro_date2=strtoupper(get_the_time( 'Y')); 
			$media=$media.'<p class="spro_cal1">'.$spro_date.'</p>';
			$media=$media.'<p class="spro_cal2">'.$spro_date2.'</p>';
			
		}
		else if($image=="yes"){
			$media='<img class="media-object" style="width: 100%; height:auto" src="'.$spro_gallery_img[0].'" />';
		}
		
		$link=get_permalink();
		
		$output=$output.'<li class="media">
							<a class="pull-left" href="'.$link.'" style="width:'.$size.'px">
							'.$media.'	
							</a>
							<div class="media-body">
								<a href="'.$link.'"><h3 class="media-heading">'.get_the_title().'</h3></a>
								';
								if($length==0)
									$output=$output.'<p>'.get_the_excerpt().'</p>';
								else {
									$str=do_shortcode(get_the_content());
									 
									$length = abs((int)$length);
								  
								   if(strlen($str) > $length) {
									  $str = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $str);
								   }
								   
									$output=$output.$str;
								}
								$output=$output.'
							</div>
						</li>';
		
	
		
		
		 endwhile;
		
		$output=$output.'</ul>';
		
		else : $output="";
		
		 endif; 
		 
wp_reset_postdata();
		 //$output2=test;
            	
	// Now we are returning the HTML code back to the place from where the shortcode was called.
	return ($output);
}


?>