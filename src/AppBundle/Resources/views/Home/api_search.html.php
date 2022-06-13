<?php 
$obj;
$channels_list =  array();
$posters_list =  array();
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
	$ch["playas"]=$channel->getPlayas();
	$ch["rating"]=$channel->getRating();
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
foreach ($posters as $key => $poster) {
		$pstr = null;
		$pstr["id"]= $poster->getId();
		$pstr["title"]= $poster->getTitle();
		$pstr["label"]= $poster->getLabel();
		$pstr["sublabel"]= $poster->getSublabel();
		$pstr["type"]= $poster->getType();
		$pstr["description"]= $poster->getDescription();
		$pstr["year"]= $poster->getYear();
		$pstr["rating"]= $poster->getRating();
		$pstr["imdb"]= $poster->getImdb();
		$pstr["duration"] = $poster->getDuration();
		$pstr["comment"]= $poster->getComment();
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
		$posters_list[]=$pstr;

}
$obj["channels"]=$channels_list;
$obj["posters"]=$posters_list;
echo json_encode($obj, JSON_UNESCAPED_UNICODE);
?>