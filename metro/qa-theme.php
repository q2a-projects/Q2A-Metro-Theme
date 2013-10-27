<?php 
	class qa_html_theme extends qa_html_theme_base
	{
	
		function header()
		{
			$this->output('<DIV CLASS="qa-header">');

			$this->logo();
				$this->nav_user_search();
				$this->header_clear();
			
				$this->output('<DIV CLASS="qa-header-nav">');
				$this->nav_main_sub();
				$this->header_clear();
				$this->output('</DIV>');
				
			$this->output('</DIV> <!-- END qa-header -->', '');
			

			
			
		}
		

		
		function main()
		{
			$content=$this->content;

			$this->output('<DIV CLASS="qa-main'.(@$this->content['hidden'] ? ' qa-main-hidden' : '').'">');
			$this->output('<DIV CLASS="qa-main-wrapper">');
			$this->widgets('main', 'top');
			
			$this->page_title_error();		
			
			$this->widgets('main', 'high');

			/*if (isset($content['main_form_tags']))
				$this->output('<FORM '.$content['main_form_tags'].'>');*/
				
			$this->main_parts($content);
		
			/*if (isset($content['main_form_tags']))
				$this->output('</FORM>');*/
				
			$this->widgets('main', 'low');

			$this->page_links();
			$this->suggest_next();
			
			$this->widgets('main', 'bottom');

			$this->output('</DIV></DIV> <!-- END qa-main -->', '');
		}
		
		function page_title_error()
		{
			$title=@$this->content['title'];
			$favorite=@$this->content['favorite'];
			
			if (isset($favorite))
				$this->output('<FORM '.$favorite['form_tags'].'>');
				
			$this->output('<H1>');

			if (isset($favorite)) {
				$this->output('<span class="qa-favoriting" '.@$favorite['favorite_tags'].'>');
				$this->favorite_inner_html($favorite);
				$this->output('</span>');
			}

			if (isset($title))

				$this->output('<span>'.$title.'</span>');

			
			if (isset($this->content['error'])) {
				$this->output('</H1>');
				$this->error(@$this->content['error']);
			} else
				$this->output('</H1>');

			if (isset($favorite)) {
				$this->form_hidden_elements(@$favorite['form_hidden']);
				$this->output('</form>');
			}
		}
		function search_field($search)
		{
			$this->output('<INPUT '.$search['field_tags'].' VALUE="'.@$search['value'].'" CLASS="qa-search-field" placeholder="Search..."/>');
		}
		
		
		function q_list($q_list)
		{
			if (isset($q_list['qs'])) {
				$this->output('<DIV CLASS="qa-q-list">', '');
				
				foreach ($q_list['qs'] as $q_item)
					$this->q_list_item($q_item);
	
				$this->output('</DIV> <!-- END qa-q-list -->', '');
			}
		}
		
		function q_list_item($q_item)
		{
			$this->output('<DIV CLASS="qa-q-list-item'.rtrim(' '.@$q_item['classes']).'" '.@$q_item['tags'].'>');

			
			$this->q_item_main($q_item);
			$this->q_item_clear();

			$this->output('</DIV> <!-- END qa-q-list-item -->', '');
		}
		
		function q_item_stats($q_item)
		{
			$this->output('<DIV CLASS="qa-q-item-stats">');
			
			$this->voting($q_item);
			$this->a_count($q_item);
			//$this->view_count($q_item);
			
			$this->output('</DIV>');
		}
		
		function q_item_main($q_item)
		{
			$this->q_item_stats($q_item);
			$this->q_item_title($q_item);
			$this->q_item_content($q_item);		

			$this->output('<DIV CLASS="qa-q-item-main-sub">');
			$this->post_avatar($q_item, 'qa-q-item');
			$this->output('<DIV CLASS="qa-q-meta-items">');
			
			$this->post_meta($q_item, 'qa-q-item');
			
			$this->post_tags($q_item, 'qa-q-item');
			
			$this->q_item_buttons($q_item);
			
			$this->output('</DIV>');
			$this->output('</DIV>');
		}
		function voting_inner_html($post)
		{
			$this->vote_buttons($post);

			$this->vote_clear();
		}
				function vote_count($post)
		{
			// You can also use $post['upvotes_raw'], $post['downvotes_raw'], $post['netvotes_raw'] to get
			// raw integer vote counts, for graphing or showing in other non-textual ways
			
			$this->output('<DIV CLASS="qa-vote-count '.(($post['vote_view']=='updown') ? 'qa-vote-count-updown' : 'qa-vote-count-net').'">');

			if ($post['vote_view']=='updown') {
				$this->output_split($post['upvotes_view'], 'qa-upvote-count');
				$this->output_split($post['downvotes_view'], 'qa-downvote-count');
			
			} else
				$this->output($post['netvotes_raw']);

			$this->output('</DIV>');
		}
		function vote_buttons($post)
		{
			$this->output('<DIV CLASS="qa-vote-buttons '.(($post['vote_view']=='updown') ? 'qa-vote-buttons-updown' : 'qa-vote-buttons-net').'">');

			switch (@$post['vote_state'])
			{
				case 'voted_up':
					$this->post_hover_button($post, 'vote_up_tags', '+', 'qa-vote-one-button qa-voted-up');
					$this->vote_count($post);
					break;
					
				case 'voted_up_disabled':
					$this->post_disabled_button($post, 'vote_up_tags', '+', 'qa-vote-one-button qa-vote-up');
					$this->vote_count($post);
					break;
					
				case 'voted_down':
					$this->post_hover_button($post, 'vote_down_tags', '&ndash;', 'qa-vote-one-button qa-voted-down');
					$this->vote_count($post);
					break;
					
				case 'voted_down_disabled':
					$this->post_disabled_button($post, 'vote_down_tags', '&ndash;', 'qa-vote-one-button qa-vote-down');
					$this->vote_count($post);					
					break;
					
				case 'up_only':
					$this->post_hover_button($post, 'vote_up_tags', '+', 'qa-vote-first-button qa-vote-up');
					$this->vote_count($post);
					$this->post_disabled_button($post, 'vote_down_tags', '', 'qa-vote-second-button qa-vote-down');
					break;
				
				case 'enabled':
					$this->post_hover_button($post, 'vote_up_tags', '+', 'qa-vote-first-button qa-vote-up');
					$this->vote_count($post);
					$this->post_hover_button($post, 'vote_down_tags', '&ndash;', 'qa-vote-second-button qa-vote-down');
					break;

				default:
					$this->post_disabled_button($post, 'vote_up_tags', '', 'qa-vote-first-button qa-vote-up');
					$this->vote_count($post);
					$this->post_disabled_button($post, 'vote_down_tags', '', 'qa-vote-second-button qa-vote-down');
					break;
			}
			$this->output('</DIV>');
		}
		

		function attribution()
		{
			// Hi there. I'd really appreciate you displaying this link on your Q2A site. Thank you - Gideon
			// fellow webmaster, please consider that it's against MIT Lisence to remove the QA-Themes attribution
			$this->output(
				'<DIV CLASS="qa-attribution">',
				'Designed by <a href="http://www.qa-themes.com" title="Q2A themes & Services">Q2A Themes</a>, ',
				'Powered by <A HREF="http://www.question2answer.org/">Question2Answer</A>',
				'</DIV>'
			);
		}
		function post_avatar($post, $class, $prefix=null)
		{
			if (isset($post['avatar'])) {
				if (isset($prefix))
					$this->output($prefix);

				$this->output('<SPAN CLASS="'.$class.'-avatar">', $post['avatar'], '</SPAN>');
			}
		}
		
		function post_meta($post, $class, $prefix=null, $separator='<BR/>')
		{
			$this->output('<SPAN CLASS="'.$class.'-meta">');
			
			if (isset($prefix))
				$this->output($prefix);
			
			$order=explode('^', @$post['meta_order']);
			
			foreach ($order as $element)
				switch ($element) {
					case 'what':
						//$this->post_meta_what($post, $class);
						break;
						
					case 'when':
						$this->post_meta_when($post, $class);
						break;
						
					case 'where':
						$this->post_meta_where($post, $class);
						break;
						
					case 'who':
						$this->post_meta_who($post, $class);
						break;
				}
			$this->post_meta_flags($post, $class);
			
			if (!empty($post['what_2'])) {
				$this->output($separator);
				
				foreach ($order as $element)
					switch ($element) {
						case 'what':
							$this->output('<SPAN CLASS="'.$class.'-what">'.$post['what_2'].'</SPAN>');
							break;
						
						case 'when':
							$this->output_split(@$post['when_2'], $class.'-when');
							break;
						
						case 'who':
							$this->output_split(@$post['who_2'], $class.'-who');
							break;
					}
			}
			$this->view_count($post);	
			$this->output('</SPAN>');
			
		}
		
		function q_view_main($q_view)
		{
			$this->output('<div class="qa-q-view-main">');

			if (isset($q_view['main_form_tags']))
				$this->output('<form '.$q_view['main_form_tags'].'>'); // form for buttons on question

			$this->q_view_content($q_view);
			$this->q_view_extra($q_view);
			$this->q_view_follows($q_view);
			$this->q_view_closed($q_view);
			$this->post_tags($q_view, 'qa-q-view');
			$this->post_avatar_meta($q_view, 'qa-q-view');
			$this->q_view_buttons($q_view);
			$this->c_list(@$q_view['c_list'], 'qa-q-view');
			
			if (isset($q_view['main_form_tags'])) {
				$this->form_hidden_elements(@$q_view['buttons_form_hidden']);
				$this->output('</form>');
			}
			
			$this->c_form(@$q_view['c_form']);
			
			$this->output('</div> <!-- END qa-q-view-main -->');
		}
		function q_view($q_view)
		{
			if (!empty($q_view)) {
				$this->output('<DIV CLASS="qa-q-view'.(@$q_view['hidden'] ? ' qa-q-view-hidden' : '').rtrim(' '.@$q_view['classes']).'"'.rtrim(' '.@$q_view['tags']).'>');
				
				if (isset($q_view['main_form_tags']))
					$this->output('<FORM '.$q_view['main_form_tags'].'>'); // form to start before voting buttons

				$this->voting($q_view);
				$this->a_count($q_view);
				$this->q_view_main($q_view);
				$this->q_view_clear();
				
				$this->output('</DIV> <!-- END qa-q-view -->', '');
			}
		}
	}	
/*
	Omit PHP closing tag to help avoid accidental output
*/