				<?php
					$data = [
					  	'chat_id' => 158754689,
					  	'text' => 'Start: '.date("H:i:s"),
					];
					$response = file_get_contents("https://api.telegram.org/bot689487990:AAGhqhcsalt0mXYRnUqFro9ECNxPuOOVPZc/sendMessage?" . http_build_query($data) );
				?>