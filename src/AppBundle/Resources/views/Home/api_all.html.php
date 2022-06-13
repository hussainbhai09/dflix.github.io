<?php 
$obj;
$channels_list =  array();
$slides_list =  array();
$genres_list =  array();
$genres_list =  array();
foreach ($channels as $key => $channel) {
	$ch = null;
	$ch["id"]=$channel->getId();
	$ch["title"]=$channel->getTitle();
	$ch["label"]=$channel->getLabel();
	$ch["sublabel"]=$channel->getSublabel();
	$ch["description"]=$channel->getDescription();
	$ch["website"]=$channel->getWebsite();
	$ch["classification"]=$channel->getClassification();
	$ch["views"]=$channel->getViews();
	$ch["shares"]=$channel->getShares();
	$ch["rating"]=$channel->getRating();
	$ch["playas"]=$channel->getPlayas();
	$ch["comment"]=$channel->getComment();
	$ch["image"] = $this['imagine']->filter($view['assets']->getUrl($channel->getMedia()->getLink()), 'channel_thumb');
	$source_channel_list = array();
	foreach ($channel->getSources() as $key => $source_channel) {
		$source_channel_obj = array();
		$source_channel_obj["id"]=$source_channel->getId();
		$source_channel_obj["title"]=$source_channel->getTitle();
		$source_channel_obj["quality"]=$source_channel->getQuality();
		$source_channel_obj["size"]=$source_channel->getSize();
		$source_channel_obj["kind"]=$source_channel->getKind();
		$source_channel_obj["premium"]=$source_channel->getPremium();
		$source_channel_obj["external"]=$source_channel->getExternal();
		
		$source_channel_obj["type"]=$source_channel->getType();
		$source_channel_obj["url"]=$source_channel->getUrl();
		
		$source_channel_list[] = $source_channel_obj;
	}
	$ch["sources"] = $source_channel_list;

	$category_channel_list = array();
	foreach ($channel->getCategories() as $key => $category_channel) {
		$category_channel_obj = array();
		$category_channel_obj["id"]=$category_channel->getId();
		$category_channel_obj["title"]=$category_channel->getTitle();		
		$category_channel_list[] = $category_channel_obj;
	}
	$ch["categories"] = $category_channel_list;

	$country_channel_list = array();
	foreach ($channel->getCountries() as $key => $country_channel) {
		$country_channel_obj = array();
		$country_channel_obj["id"]=$country_channel->getId();
		$country_channel_obj["title"]=$country_channel->getTitle();		
		$country_channel_obj["image"] = $this['imagine']->filter($view['assets']->getUrl($country_channel->getMedia()->getLink()), 'country_thumb');

		$country_channel_list[] = $country_channel_obj;
	}
	$ch["countries"] = $country_channel_list;

	$channels_list[]=$ch;
}
foreach ($slides as $key => $slide) {
	$slide_obj = null;
	$slide_obj["id"]=$slide->getId();
	$slide_obj["title"]=$slide->getClear();
	$slide_obj["type"]=$slide->getType();
	$slide_obj["image"] = $this['imagine']->filter($view['assets']->getUrl($slide->getMedia()->getLink()), 'slide_thumb');
	if ($slide->getType() == "1" && $slide->getUrl() != null) {
		$slide_obj["url"]= $slide->getUrl();
	}
	if ($slide->getType() == "2" && $slide->getCategory() != null) {
		$category_obj = null;
		$category_obj["id"]=$slide->getCategory()->getId();
		$category_obj["title"]=$slide->getCategory()->getTitle();	
		$slide_obj["category"]= $category_obj;
	}
	if ($slide->getType() == "5" && $slide->getGenre() != null) {
		$genre_obj = null;
		$genre_obj["id"]=$slide->getGenre()->getId();
		$genre_obj["title"]=$slide->getGenre()->getTitle();
		$slide_obj["genre"]= $genre_obj;
	}
	if ($slide->getType() == "3" && $slide->getChannel() != null) {
		$ch = null;
		$ch["id"]=$slide->getChannel()->getId();
		$ch["title"]=$slide->getChannel()->getTitle();
		$ch["label"]=$slide->getChannel()->getLabel();
		$ch["sublabel"]=$slide->getChannel()->getSublabel();
		$ch["description"]=$slide->getChannel()->getDescription();
		$ch["website"]=$slide->getChannel()->getWebsite();
		$ch["classification"]=$slide->getChannel()->getClassification();
		$ch["views"]=$slide->getChannel()->getViews();
		$ch["playas"]=$slide->getChannel()->getPlayas();

		$ch["shares"]=$slide->getChannel()->getShares();
		$ch["rating"]=$slide->getChannel()->getRating();
		$ch["comment"]=$slide->getChannel()->getComment();
		$ch["image"] = $this['imagine']->filter($view['assets']->getUrl($slide->getChannel()->getMedia()->getLink()), 'channel_thumb');
		$source_channel_list = array();
		foreach ($slide->getChannel()->getSources() as $key => $source_channel) {
			$source_channel_obj = array();
			$source_channel_obj["id"]=$source_channel->getId();
			$source_channel_obj["title"]=$source_channel->getTitle();
			$source_channel_obj["quality"]=$source_channel->getQuality();
			$source_channel_obj["size"]=$source_channel->getSize();
			$source_channel_obj["kind"]=$source_channel->getKind();
			$source_channel_obj["premium"]=$source_channel->getPremium();
			$source_channel_obj["external"]=$source_channel->getExternal();
			
			$source_channel_obj["type"]=$source_channel->getType();
			$source_channel_obj["url"]=$source_channel->getUrl();
			
			$source_channel_list[] = $source_channel_obj;
		}
		$ch["sources"] = $source_channel_list;

		$category_channel_list = array();
		foreach ($slide->getChannel()->getCategories() as $key => $category_channel) {
			$category_channel_obj = array();
			$category_channel_obj["id"]=$category_channel->getId();
			$category_channel_obj["title"]=$category_channel->getTitle();		
			$category_channel_list[] = $category_channel_obj;
		}
		$ch["categories"] = $category_channel_list;

		$country_channel_list = array();
		foreach ($channel->getCountries() as $key => $country_channel) {
			$country_channel_obj = array();
			$country_channel_obj["id"]=$country_channel->getId();
			$country_channel_obj["title"]=$country_channel->getTitle();		
			$country_channel_obj["image"] = $this['imagine']->filter($view['assets']->getUrl($country_channel->getMedia()->getLink()), 'country_thumb');

			$country_channel_list[] = $country_channel_obj;
		}
		$ch["countries"] = $country_channel_list;
		$slide_obj["channel"]= $ch;

	}
	if ($slide->getType() == "4" && $slide->getPoster() != null) {
		$pstr = null;
		$pstr["id"]= $slide->getPoster()->getId();
		$pstr["title"]= $slide->getPoster()->getTitle();
		$pstr["label"]= $slide->getPoster()->getLabel();
		$pstr["sublabel"]= $slide->getPoster()->getSublabel();
		$pstr["type"]= $slide->getPoster()->getType();
		$pstr["description"]= $slide->getPoster()->getDescription();
		$pstr["year"]= $slide->getPoster()->getYear();
		$pstr["imdb"]= $slide->getPoster()->getImdb();

		$pstr["rating"]= $slide->getPoster()->getRating();
		$pstr["duration"] = $slide->getPoster()->getDuration();
		$pstr["downloadas"] = $slide->getPoster()->getDownloadas();
		$pstr["comment"] = $slide->getPoster()->getComment();
		$pstr["playas"] = $slide->getPoster()->getPlayas();	
		$pstr["classification"]= $slide->getPoster()->getClassification();
		$pstr["image"] = $this['imagine']->filter($view['assets']->getUrl($slide->getPoster()->getPoster()->getLink()), 'poster_thumb');
		if($slide->getPoster()->getCover())
			$pstr["cover"] = $this['imagine']->filter($view['assets']->getUrl($slide->getPoster()->getCover()->getLink()), 'cover_thumb');
	
		$genre_poster_list =  array();
		foreach ($slide->getPoster()->getGenres() as $key => $genre_poster) {
			$genre_poster_obj = array();
			$genre_poster_obj["id"]=$genre_poster->getId();
			$genre_poster_obj["title"]=$genre_poster->getTitle();
			$genre_poster_list[] = $genre_poster_obj;
		}
		$pstr["genres"] = $genre_poster_list;

		if($slide->getPoster()->getTrailer()){
			$trailer_poster_obj["id"]=$slide->getPoster()->getTrailer()->getId();
			if ($slide->getPoster()->getTrailer()->getType()=="file") {
				$trailer_poster_obj["url"]=$app->getRequest()->getScheme()."://".$app->getRequest()->getHttpHost()."/". $slide->getPoster()->getTrailer()->getMedia()->getLink();
				$trailer_poster_obj["type"]=$slide->getPoster()->getTrailer()->getMedia()->getExtension();

			}else{
				$trailer_poster_obj["type"]=$slide->getPoster()->getTrailer()->getType();
				$trailer_poster_obj["url"]=$slide->getPoster()->getTrailer()->getUrl();
			}
			$pstr["trailer"] = $trailer_poster_obj;
		}

		$source_poster_list =  array();
		foreach ($slide->getPoster()->getSources() as $key => $source_poster) {
			$source_poster_obj = array();
			$source_poster_obj["id"]=$source_poster->getId();
			$source_poster_obj["title"]=$source_poster->getTitle();
			$source_poster_obj["quality"]=$source_poster->getQuality();
			$source_poster_obj["size"]=$source_poster->getSize();
			$source_poster_obj["kind"]=$source_poster->getKind();
			$source_poster_obj["premium"]=$source_poster->getPremium();
			$source_poster_obj["external"]=$source_poster->getExternal();
			if ($source_poster->getType()=="file") {
				$source_poster_obj["url"]=$app->getRequest()->getScheme()."://".$app->getRequest()->getHttpHost()."/". $source_poster->getMedia()->getLink();
				$source_poster_obj["type"]=$source_poster->getMedia()->getExtension();
			}else{
				$source_poster_obj["type"]=$source_poster->getType();
				$source_poster_obj["url"]=$source_poster->getUrl();
			}
			$source_poster_list[] = $source_poster_obj;
		}
		$pstr["sources"] = $source_poster_list;
		$slide_obj["poster"]= $pstr;
	}
	$slides_list[]=$slide_obj;
}

$genre_obj["id"]=-1;
$genre_obj["title"]="Top Rated ";
$posters = array();

foreach ($bestrated as $key => $poster) {
		$pstr = null;
		$pstr["id"]= $poster->getId();
		$pstr["title"]= $poster->getTitle();
		$pstr["label"]= $poster->getLabel();
		$pstr["sublabel"]= $poster->getSublabel();
		$pstr["type"]= $poster->getType();
		$pstr["description"]= $poster->getDescription();
		$pstr["year"]= $poster->getYear();
		$pstr["imdb"]= $poster->getImdb();
		$pstr["rating"]= $poster->getRating();
		$pstr["comment"]= $poster->getComment();
		$pstr["duration"] = $poster->getDuration();
		$pstr["downloadas"] = $poster->getDownloadas();
		$pstr["playas"] = $poster->getPlayas();	
		$pstr["classification"]= $poster->getClassification();
		$pstr["image"] = $this['imagine']->filter($view['assets']->getUrl($poster->getPoster()->getLink()), 'poster_thumb');
		if($poster->getCover())
			$pstr["cover"] = $this['imagine']->filter($view['assets']->getUrl($poster->getCover()->getLink()), 'cover_thumb');
	
		$genre_poster_list =  array();
		foreach ($poster->getGenres() as $key => $genre_poster) {
			$genre_poster_obj = array();
			$genre_poster_obj["id"]=$genre_poster->getId();
			$genre_poster_obj["title"]=$genre_poster->getTitle();
			$genre_poster_list[] = $genre_poster_obj;
		}
		$pstr["genres"] = $genre_poster_list;

		if($poster->getTrailer()){
			$trailer_poster_obj["id"]=$poster->getTrailer()->getId();
			if ($poster->getTrailer()->getType()=="file") {
				$trailer_poster_obj["url"]=$app->getRequest()->getScheme()."://".$app->getRequest()->getHttpHost()."/". $poster->getTrailer()->getMedia()->getLink();
				$trailer_poster_obj["type"]=$poster->getTrailer()->getMedia()->getExtension();

			}else{
				$trailer_poster_obj["type"]=$poster->getTrailer()->getType();
				$trailer_poster_obj["url"]=$poster->getTrailer()->getUrl();
			}
			$pstr["trailer"] = $trailer_poster_obj;
		}

		$source_poster_list =  array();
		foreach ($poster->getSources() as $key => $source_poster) {
			$source_poster_obj = array();
			$source_poster_obj["id"]=$source_poster->getId();
			$source_poster_obj["title"]=$source_poster->getTitle();
			$source_poster_obj["quality"]=$source_poster->getQuality();
			$source_poster_obj["size"]=$source_poster->getSize();
			$source_poster_obj["kind"]=$source_poster->getKind();
			$source_poster_obj["premium"]=$source_poster->getPremium();
			$source_poster_obj["external"]=$source_poster->getExternal();
			if ($source_poster->getType()=="file") {
				$source_poster_obj["url"]=$app->getRequest()->getScheme()."://".$app->getRequest()->getHttpHost()."/". $source_poster->getMedia()->getLink();
				$source_poster_obj["type"]=$source_poster->getMedia()->getExtension();

			}else{
				$source_poster_obj["type"]=$source_poster->getType();
				$source_poster_obj["url"]=$source_poster->getUrl();
			}
			$source_poster_list[] = $source_poster_obj;
		}
		$pstr["sources"] = $source_poster_list;

		$posters[]=$pstr;
	

}
$genre_obj["posters"] = $posters;
$genres_list[]=$genre_obj;
$genre_obj = null;
$genre_obj["id"]=0;
$genre_obj["title"]="Popular";
$posters = array();
foreach ($popular as $key => $poster) {
		$pstr = null;
		$pstr["id"]= $poster->getId();
		$pstr["title"]= $poster->getTitle();
		$pstr["label"]= $poster->getLabel();
		$pstr["sublabel"]= $poster->getSublabel();
		$pstr["type"]= $poster->getType();
		$pstr["imdb"]= $poster->getImdb();
		$pstr["description"]= $poster->getDescription();
		$pstr["year"]= $poster->getYear();
		$pstr["comment"]= $poster->getComment();
		$pstr["rating"]= $poster->getRating();
		$pstr["duration"] = $poster->getDuration();
		$pstr["downloadas"] = $poster->getDownloadas();
		$pstr["playas"] = $poster->getPlayas();
		$pstr["classification"]= $poster->getClassification();
		$pstr["image"] = $this['imagine']->filter($view['assets']->getUrl($poster->getPoster()->getLink()), 'poster_thumb');
		if($poster->getCover())
			$pstr["cover"] = $this['imagine']->filter($view['assets']->getUrl($poster->getCover()->getLink()), 'cover_thumb');
	
		$genre_poster_list =  array();
		foreach ($poster->getGenres() as $key => $genre_poster) {
			$genre_poster_obj = array();
			$genre_poster_obj["id"]=$genre_poster->getId();
			$genre_poster_obj["title"]=$genre_poster->getTitle();
			$genre_poster_list[] = $genre_poster_obj;
		}
		$pstr["genres"] = $genre_poster_list;

		if($poster->getTrailer()){
			$trailer_poster_obj["id"]=$poster->getTrailer()->getId();
			if ($poster->getTrailer()->getType()=="file") {
				$trailer_poster_obj["url"]=$app->getRequest()->getScheme()."://".$app->getRequest()->getHttpHost()."/". $poster->getTrailer()->getMedia()->getLink();
				$trailer_poster_obj["type"]=$poster->getTrailer()->getMedia()->getExtension();

			}else{
				$trailer_poster_obj["type"]=$poster->getTrailer()->getType();
				$trailer_poster_obj["url"]=$poster->getTrailer()->getUrl();
			}
			$pstr["trailer"] = $trailer_poster_obj;
		}

		$source_poster_list =  array();
		foreach ($poster->getSources() as $key => $source_poster) {
			$source_poster_obj = array();
			$source_poster_obj["id"]=$source_poster->getId();
			$source_poster_obj["title"]=$source_poster->getTitle();
			$source_poster_obj["quality"]=$source_poster->getQuality();
			$source_poster_obj["size"]=$source_poster->getSize();
			$source_poster_obj["kind"]=$source_poster->getKind();
			$source_poster_obj["premium"]=$source_poster->getPremium();
			$source_poster_obj["external"]=$source_poster->getExternal();
			if ($source_poster->getType()=="file") {
				$source_poster_obj["url"]=$app->getRequest()->getScheme()."://".$app->getRequest()->getHttpHost()."/". $source_poster->getMedia()->getLink();
				$source_poster_obj["type"]=$source_poster->getMedia()->getExtension();

			}else{
				$source_poster_obj["type"]=$source_poster->getType();
				$source_poster_obj["url"]=$source_poster->getUrl();
			}
			$source_poster_list[] = $source_poster_obj;
		}
		$pstr["sources"] = $source_poster_list;

		$posters[]=$pstr;
	

}
$genre_obj["posters"] = $posters;
$genres_list[]=$genre_obj;

foreach ($genres as $key => $genre) {
	$genre_obj = null;
	$genre_obj["id"]=$genre->getId();
	$genre_obj["title"]=$genre->getTitle();
	$posters = array();
	$count = 0;
	foreach ($genre->getPosters() as $key => $poster) {
		if ($count<15 && $poster->getEnabled()) {
			$count++;
			$pstr = null;
			$pstr["id"]= $poster->getId();
			$pstr["title"]= $poster->getTitle();
			$pstr["label"]= $poster->getLabel();
			$pstr["sublabel"]= $poster->getSublabel();
			$pstr["type"]= $poster->getType();
			$pstr["description"]= $poster->getDescription();
			$pstr["year"]= $poster->getYear();
			$pstr["rating"]= $poster->getRating();
			$pstr["comment"]= $poster->getComment();
			$pstr["imdb"]= $poster->getImdb();
			$pstr["duration"] = $poster->getDuration();
			$pstr["downloadas"] = $poster->getDownloadas();
			$pstr["playas"] = $poster->getPlayas();
			$pstr["classification"]= $poster->getClassification();
			$pstr["image"] = $this['imagine']->filter($view['assets']->getUrl($poster->getPoster()->getLink()), 'poster_thumb');
			if($poster->getCover())
				$pstr["cover"] = $this['imagine']->filter($view['assets']->getUrl($poster->getCover()->getLink()), 'cover_thumb');
		
			$genre_poster_list =  array();
			foreach ($poster->getGenres() as $key => $genre_poster) {
				$genre_poster_obj = array();
				$genre_poster_obj["id"]=$genre_poster->getId();
				$genre_poster_obj["title"]=$genre_poster->getTitle();
				$genre_poster_list[] = $genre_poster_obj;
			}
			$pstr["genres"] = $genre_poster_list;

			if($poster->getTrailer()){
				$trailer_poster_obj["id"]=$poster->getTrailer()->getId();
				if ($poster->getTrailer()->getType()=="file") {
					$trailer_poster_obj["url"]=$app->getRequest()->getScheme()."://".$app->getRequest()->getHttpHost()."/". $poster->getTrailer()->getMedia()->getLink();
					$trailer_poster_obj["type"]=$poster->getTrailer()->getMedia()->getExtension();

				}else{
					$trailer_poster_obj["type"]=$poster->getTrailer()->getType();
					$trailer_poster_obj["url"]=$poster->getTrailer()->getUrl();
				}
				$pstr["trailer"] = $trailer_poster_obj;
			}

			$source_poster_list =  array();
			foreach ($poster->getSources() as $key => $source_poster) {
				$source_poster_obj = array();
				$source_poster_obj["id"]=$source_poster->getId();
				$source_poster_obj["title"]=$source_poster->getTitle();
				$source_poster_obj["quality"]=$source_poster->getQuality();
				$source_poster_obj["size"]=$source_poster->getSize();
				$source_poster_obj["kind"]=$source_poster->getKind();
				$source_poster_obj["premium"]=$source_poster->getPremium();
				$source_poster_obj["external"]=$source_poster->getExternal();
				if ($source_poster->getType()=="file") {
					$source_poster_obj["url"]=$app->getRequest()->getScheme()."://".$app->getRequest()->getHttpHost()."/". $source_poster->getMedia()->getLink();
					$source_poster_obj["type"]=$source_poster->getMedia()->getExtension();

				}else{
					$source_poster_obj["type"]=$source_poster->getType();
					$source_poster_obj["url"]=$source_poster->getUrl();
				}
				$source_poster_list[] = $source_poster_obj;
			}
			$pstr["sources"] = $source_poster_list;

			$posters[]=$pstr;
		}

	}
	$genre_obj["posters"] = $posters;
	$genres_list[]=$genre_obj;

}
$actors_list=array();
foreach ($actors as $key => $actor) {
          $actor_obj["id"]=$actor["id"];
          $actor_obj["type"]=$actor["type"];
          $actor_obj["name"]=$actor["name"];
          $actor_obj["bio"]=$actor["bio"];
          $actor_obj["height"]=$actor["height"];
          $actor_obj["born"]=$actor["born"];
	$actor_obj["image"] = $this['imagine']->filter($view['assets']->getUrl("uploads/".$actor["extension"]."/".$actor["image"]), 'actor_thumb');
	$actors_list[]=$actor_obj;
}

$obj["channels"]=$channels_list;
$obj["slides"]=$slides_list;
$obj["genres"]=$genres_list;
$obj["actors"]=$actors_list;
echo json_encode($obj, JSON_UNESCAPED_UNICODE);
?>