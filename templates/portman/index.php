<?php
/**

 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$user = JFactory::getUser();
$db = JFactory::getDBO();
$home = JRequest::getVar('Itemid');
		
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" type="text/css" href="templates/<?php echo $this->template;?>/css/bootstrap.css"/>
	<link rel="stylesheet" type="text/css" href="templates/<?php echo $this->template;?>/css/colorbox.css"/>
	
	<link rel="stylesheet" type="text/css" href="templates/<?php echo $this->template;?>/css/local-projects.css"/>
	<link rel="stylesheet" type="text/css" href="templates/<?php echo $this->template;?>/css/mobile.css"/>
	<link rel="stylesheet" type="text/css" href="templates/<?php echo $this->template;?>/css/jquery.jscrollpane.css"/>
	<link rel="stylesheet" type="text/css" href="templates/<?php echo $this->template;?>/css/customSelectBox.css"/>
	<link rel="stylesheet" type="text/css" href="templates/<?php echo $this->template;?>/css/template.css"/>
	<jdoc:include type="head" />
	<?php 
		
		if($home == 101){
	?>
	<script src="templates/<?php echo $this->template;?>/js/jquery_1.9.js"></script>
	<?php } ?>
	<script src="templates/<?php echo $this->template;?>/js/bootstrap.js"></script>
	<script src="templates/<?php echo $this->template;?>/js/bootstrap.combobox.js"></script>
	<script src="templates/<?php echo $this->template;?>/js/colorbox.js"></script>
	<script src="templates/<?php echo $this->template;?>/js/jScrollPane.js"></script>
	<script src="templates/<?php echo $this->template;?>/js/jquery.mousewheel.js"></script>
	<script src="templates/<?php echo $this->template;?>/js/SelectBox.js"></script>
	
	<script src="templates/<?php echo $this->template;?>/js/template.js"></script>


	
	<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
		
	<div class="container">
		<div class="row header">
		  <div class="col-sm-6">
		  	<jdoc:include type="modules" name="header_left" style="xhtml" />
		  </div>
		  <div class="col-sm-6">
		  		<jdoc:include type="modules" name="header_right" style="xhtml" />
		  		<jdoc:include type="modules" name="social" style="xhtml" />
				<div id="membership">
					<?php if($user->id){?>
						<strong class="current_user"><?php echo JText::_('HI')?>, <?php echo $user->name;?></strong>
						<?php
						$query = "SELECT id FROM #__iproperty_agents WHERE user_id=".$user->id;
						$db->setQuery($query);
						$agent_id = $db->loadResult();
						?>
						<div class="list-group">
							<?php if($agent_id==0){?>
								<a class="list-group-item" href="<?php echo JRoute::_('index.php?option=com_fwuser&view=info&Itemid=220&lang=en');?>"><?php echo JText::_('Member Detail')?></a>
							<?php }else{?>
							<!--<a class="list-group-item" href="<!?php echo JRoute::_('index.php?option=com_iproperty&view=agentproperty&Itemid=232&lang=en&id='.$agent_id);?>"><!?php echo JText::_('LISTING_PROPERTIES')?></a>
							<a class="list-group-item" href="<!?php echo JRoute::_('index.php?option=com_iproperty&view=propagentform&Itemid=215&lang=en')?>">
								<!?php echo JText::_('ADD_PROPERTY')?>
							</a>-->
							<a class="list-group-item" href="<?php echo JRoute::_('index.php?option=com_iproperty&view=file&Itemid=224&lang=en&id='.$agent_id);?>"><?php echo JText::_('Download template')?></a>
							<?php }?>
							<a class="list-group-item" href="<?php echo JRoute::_('index.php?option=com_fwuser&view=logout&')?>">
								<?php echo JText::_('Logout')?>
							</a>
						</div>
					<?php }else{
					?>
						<strong><a class="button-wborder" href="<?php echo JRoute::_('index.php?option=com_fwuser&view=register');?>"><?php echo JText::_('JOIN_MEMBER_FREE');?></a></strong><br />
						<?php echo JText::_('ALREADY_MEMBER');?> <a class="member" href="#member-form"><?php echo JText::_('SIGN_IN_HERE');?></a></p>
					<?php }?>
					<p>
				</div>
		  		<jdoc:include type="modules" name="user_info" style="xhtml" />
		  </div> 
		</div>
		<div id="top-navigation">
			<nav class="navbar navbar-default" role="navigation">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<jdoc:include type="modules" name="main_menu" style="none" />
				</div>
			</nav>
		</div>
		<div class="row search">
			<jdoc:include type="modules" name="search" style="none" />
		</div>
		<div class="clearfix row" id="main-container">
			<div class="col-sm-12">
				<div class="row">
					<div class="col-sm-12">
						<jdoc:include type="message" />
						<?php if ($this->countModules( 'member' )) : ?>
							<!--<div style="padding-top: 5%;padding-bottom: 5%;	"><strong class="current_user"><!?php echo JText::_('Member Name: ')?> <!?php echo $user->name;?></strong></div>-->
						<?php endif; ?>
						<!--if user is agent-->
						<?php 
							if($user->id){
								$query = "SELECT * FROM #__iproperty_agents WHERE user_id=".$user->id;
								$db->setQuery($query);
								$agent = $db->loadObjectList();
								
								if(!empty($agent)){
						?>
								<?php if ($this->countModules( 'saleperson' )) : ?>
								<!--<div class="row" style="margin-top: 3%; margin-bottom: 3%;">
									<div class="row" style="margin-left: 0px;">
										<div class="col-md-6"><span style="font-weight: bold;">Agent Name: <!?php echo $agent[0]->fname ; ?></span></div>
										<div class="col-md-6"><span style="font-weight: bold;">Last Login: <!?php echo $user->lastvisitDate ; ?></span></div>
									</div>
									<div class="row" style="margin-top: 3%;margin-left: 0px;">
										<div class="col-md-6"><span style="font-weight: bold;">CEA Number: <!?php echo $agent[0]->id; ?></span></div>
										<div class="col-md-6"></div>
									</div>
								</div>-->
								<?php endif; ?>
						<?php
								}
							}
						?>
						<jdoc:include type="modules" name="member"/>
						<jdoc:include type="modules" name="saleperson"/>
						
						<jdoc:include type="component" />
					</div>
				</div>
			</div>
			<!-- <div class="col-sm-3">
				<jdoc:include type="modules" name="right" style="xhtml" />
			</div>  -->
		</div>
	</div>	
	<div class="container" style="padding:0;">
		<div class="row" id="footer">
			<div class="col-sm-12">
				<jdoc:include type="modules" name="footer"/>
			</div>
			
			
		</div>	
	</div>
	<jdoc:include type="modules" name="debug" style="none" />
	<jdoc:include type="modules" name="login" style="none" />
</body>
</html>