<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 Andy Sharman @ udjamaflip.com
 * @modified and rewritten by the Thinkery
 * @license see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

class modIpTagCloudHelper
{	
	public static function getWords(&$params)
	{		
    	$db     = JFactory::getDbo();
        $user   = JFactory::getUser();
        $groups	= $user->getAuthorisedViewLevels();
        
        // set params from module
        $limit  = $params->get('limit', 20);
        $cat    = $params->get('cat_id', 0);
        
        // set columns to pull from
        $cols   = array();
        if($params->get('colpstreet', 1))      $cols[] = 'p.street';
        if($params->get('colptitle', 1))       $cols[] = 'p.title';
        if($params->get('colpshort', 1))       $cols[] = 'p.short_description';
        if($params->get('colpdesc', 1))        $cols[] = 'p.description';
        if($params->get('colpcity', 1))        $cols[] = 'p.city';
        if($params->get('colpprovince', 1))    $cols[] = 'p.province';
        if($params->get('colctitle', 1))       $cols[] = 'c.title';
        if($params->get('colcdesc', 1))        $cols[] = 'c.desc';
        if($params->get('colpcounty', 1))      $cols[] = 'p.county';
        if($params->get('colpregion', 1))      $cols[] = 'p.region';
        $cols = (count($cols)) ? implode(',', $cols) : 'p.*, c.*';
        
        // character set to utf8
        $query  = 'SET CHARACTER SET utf8';
    	$db->setQuery($query);
		$db->execute();
    	
    	$query  = 'SET NAMES utf8';
    	$db->setQuery($query);
		$db->execute();
        
        // Filter by start and end dates.
        $nullDate   = $db->Quote($db->getNullDate());
        $date       = JFactory::getDate();
        $nowDate    = $db->Quote($date->toSql());
        
        // Get words query        
        $query = $db->getQuery(true);
        $query->select($cols)
                ->from('#__iproperty as p')
                ->leftJoin('#__iproperty_propmid as pm on pm.prop_id = p.id')
                ->leftJoin('#__iproperty_categories as c on c.id = pm.cat_id')
                ->where('(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$nowDate.')')
                ->where('(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$nowDate.')')
                ->where('(c.publish_up = '.$nullDate.' OR c.publish_up <= '.$nowDate.')')
                ->where('(c.publish_down = '.$nullDate.' OR c.publish_down >= '.$nowDate.')');
        if($cat && $cat != 0){
            $query->where('pm.cat_id = '.(int)$cat);
        }
        if(is_array($groups) && !empty($groups)){
            $query->where('p.access IN ('.implode(",", $groups).')')
                ->where('c.access IN ('.implode(",", $groups).')');
        }
        $query->order('RAND()');

		$db->setQuery($query, 0, $limit);
		if ($results = $db->loadObjectList()){
			// place them into 1 string without html
			$wordList = modIpTagCloudHelper::concatonateWords($results);
		}else{
			$wordList = '';
		}

		return $wordList;
	}
	
	public static function concatonateWords($dataObj)
	{
		$words = '';
		foreach ($dataObj as $row)
		{
			foreach ($row as $item)
			{
				$words .= ' '.strip_tags(utf8_encode($item));
			}			
		}
		return $words;		
	}
	
	public static function filterWords($input, $additionalWords = '', $excludeNonAlph = '')
	{
		$commonWords = array('a','able','about','above','according','accordingly',
            'across','actually','adj','after','afterwards','again','against','ago',
            'ahead','ain\'t','all','allow','allows','almost','alone','along','alongside',
            'already','also','although','always','am','amid','amidst','among','amongst',
            'an','and','another','any','anybody','anyhow','anyone','anything','anyway',
            'anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t',
            'around','as','a\'s','aside','ask','asking','associated','at','available','away',
            'awfully','b','back','backward','backwards','be','became','because','become',
            'becomes','becoming','been','before','beforehand','begin','behind','being',
            'believe','below','beside','besides','best','better','between','beyond','both',
            'brief','but','by','c','came','can','cannot','cant','can\'t','caption','cause',
            'causes','certain','certainly','changes','clearly','c\'mon','co','co.','com',
            'come','comes','concerning','consequently','consider','considering','contain',
            'containing','contains','corresponding','could','couldn\'t','course','c\'s',
            'currently','d','dare','daren\'t','definitely','described','despite','did',
            'didn\'t','different','directly','do','does','doesn\'t','doing','done','don\'t',
            'dont','down','downwards','during','e','each','edu','eg','eight','eighty','either',
            'else','elsewhere','end','ending','enough','entirely','especially','et','etc','even',
            'ever','evermore','every','everybody','everyone','everything','everywhere','ex',
            'exactly','example','except','f','fairly','far','farther','few','fewer','fifth',
            'find','first','five','followed','following','follows','for','forever','former',
            'formerly','forth','forward','found','four','from','further','furthermore','g',
            'get','gets','getting','given','gives','go','goes','going','gone','got','gotten',
            'greetings','h','had','hadn\'t','half','happens','hardly','has','hasn\'t','have',
            'haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here',
            'hereafter','hereby','herein','here\'s','hereupon','hers','herself','he\'s','hi',
            'him','himself','his','hither','hopefully','how','howbeit','however','hundred','i',
            'i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.',
            'indeed','indicate','indicated','indicates','inner','inside','insofar','instead',
            'into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve',
            'j','just','k','keep','keeps','kept','know','known','knows','l','last','lately',
            'later','latter','latterly','least','less','lest','let','let\'s','like','liked',
            'likely','likewise','little','look','looking','looks','lot','lot\'s','low','lower',
            'ltd','m','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean',
            'meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','more',
            'moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','n',
            'name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither',
            'never','neverf','neverless','nevertheless','new','next','nine','ninety','no',
            'nobody','non','none','nonetheless','noone','no-one','nor','normally','not',
            'nothing','notwithstanding','novel','now','nowhere','o','obviously','of','off',
            'often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto',
            'opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours',
            'ourselves','out','outside','over','overall','own','p','page','particular',
            'particularly','past','per','perhaps','placed','please','plus','possible',
            'presumably','probably','provided','provides','q','que','quite','qv','r','rather',
            'rd','re','really','reasonably','recent','recently','regarding','regardless',
            'regards','relatively','respectively','right','round','s','said','same','saw',
            'say','saying','says','second','secondly','see','seeing','seem','seemed','seeming',
            'seems','seen','self','selves','sensible','sent','serious','seriously','seven',
            'several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t',
            'since','six','so','some','somebody','someday','somehow','someone','something',
            'sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify',
            'specifying','still','sub','such','sup','sure','t','take','taken','taking','tell',
            'tends','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s',
            'that\'ve','the','their','theirs','them','themselves','then','thence','there',
            'thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re',
            'theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll',
            'they\'re','they\'ve','thing','things','think','third','thirty','this','thorough',
            'thoroughly','those','though','three','through','throughout','thru','thus','till',
            'to','together','too','took','toward','towards','tried','tries','truly','try',
            'trying','t\'s','twice','two','u','un','under','underneath','undoing',
            'unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards',
            'us','use','used','useful','uses','using','usually','v','value','various','versus',
            'very','via','viz','vs','w','want','wants','was','wasn\'t','way','we','we\'d',
            'welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what',
            'whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where',
            'whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever',
            'whether','which','whichever','while','whilst','whither','who','who\'d','whoever',
            'whole','who\'ll','whom','whomever','who\'s','whose','why','will','willing','wish',
            'with','within','without','work','wonder','won\'t','would','wouldn\'t','x','y','yes',
            'yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves',
            'you\'ve','z','zero');
		
		if (!empty($additionalWords))
		{
			$additionalWords = $additionalWords;
			$additionalWords = explode(',',$additionalWords);
			$commonWords = array_merge($commonWords, $additionalWords);
		}
        $commonWords = array_map('trim', $commonWords);
		
		// convert everything to lower case for ease in next parts.
		$input = strtolower($input);
		
		// remove all non alpha chars, 0-9.,!/? etc
        if($excludeNonAlph){
            $input = preg_replace('/[^a-z\s]/', '', $input);
        }
		
		// strip out common words.
		$input = preg_replace('/\b('.implode('|',$commonWords).')\b/','',$input);
		
		return $input;
	}
	
	public static function parseString($string, $count = 25)
	{
		$wordArray  = explode(' ', $string);
		$topList    = array();

		foreach ($wordArray as $value)
		{
			$value = trim($value,'	');
			$value = str_replace("\n","",$value);
			if (!empty($value) && $value != '' && strlen($value) > 2)
			{
				if (isset($topList[$value]))
				{
					$tmp = explode('~',$topList[$value]);
					$tmp[0]++;
					if (strlen($tmp[0]) == 1) { $tmp[0] = '0'.$tmp[0];}
					$topList[$value] = $tmp[0].'~'.$tmp[1];
				}
				else
				{
					$topList[$value] = '01~'.$value;
				}

			}
		}
		
		// sorts the array descending and only returns the ones the
		// amount the user wants.
		rsort($topList);
		
		$i = 1;
		$finalList = array();

        // check if result count is greater than amount set in params
        // if so, set limit as parm count
        // if not, set limit as result count
        $count = (count($topList) >= $count) ? $count : count($topList);
		while ($i <= $count)
		{
			if (strlen($topList[$i-1]) > 3)
			{
				array_push($finalList,$topList[$i-1]);
			}
			$i++;
		}

		return $finalList;
	}

	public static function outputWords($array, $minSize = 10, $maxSize = 25, $tagcolor = '#135cae')
	{
		$biggest        = explode('~', $array[0]);
		$smallest       = explode('~', $array[count($array)-1]);

		$biggest        = $biggest[0];
		$smallest       = $smallest[0];
		$difference     = $biggest - $smallest;
        $difference     = ($difference < 1) ? 1 : $difference;
		$fontDifference = $maxSize-$minSize;

		// randomizes the content
		shuffle($array);

		foreach ($array as $word)
		{
			$details    = explode('~',$word);
			$percent    = round(($details[0] - $smallest) / $difference,1);
			$fontSize   = round($minSize + ($fontDifference*$percent));
			$url        = JRoute::_('index.php?searchword='.utf8_decode($details[1]).'&ordering=searchphrase=all&option=com_search');
			
            echo '<a href="'.$url.'" style="display:inline-block; padding-right:'.rand(1,7).'px; padding-bottom:'.rand(1,7).'px; font-size:'.$fontSize.'px; color: '.$tagcolor.';">'.utf8_decode($details[1]).'</a> ';
		}
	}	
}