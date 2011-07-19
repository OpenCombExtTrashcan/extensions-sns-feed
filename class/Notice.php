<?php
namespace oc\ext\blog ;

use jc\auth\IdManager;

use jc\mvc\model\db\orm\PrototypeAssociationMap;

use jc\db\DB ;
use jc\db\PDODriver ;

use oc\ext\Extension;

class Blog extends Extension
{
	public function load()
	{
    	// 取得模型关系图的单件实例
        $aAssocMap = PrototypeAssociationMap::singleton() ;
    	$aAssocMap->addOrm(
                	array(
                		'keys' => 'bid' ,
                		'table' => 'blog' ,
                		'hasAndBelongsToMany' => array(
							array(
								'prop' => 'tag' ,
								'fromk' => 'bid' ,
								'btok' => 'bid' ,
								'bfromk' => 'tid' ,
								'tok' => 'tid' ,
								'bridge' => 'blog_link' ,
								'model' => 'blog_tag',
							) ,
						),
                	
                	)
        ) ;
		
        $aAssocMap->addOrm(
	            	array(
	            		'keys' => 'id' ,
	            		'table' => 'blog_link' ,
	            		'belongsTo' => array(
	            			array(
                				'prop' => 'blog' ,
                				'fromk' => 'bid' ,
                				'tok' => 'bid' ,
                				'model' => 'blog'
	            			),
	            		),
	            	)
        );
		
        $aAssocMap->addOrm(
	            	array(
	            		'keys' => 'tid' ,
	            		'table' => 'blog_tag' ,
	            		'hasAndBelongsToMany' => array(
	            			array(
									'prop' => 'blog' ,
									'fromk' => 'tid' ,
									'btok' => 'tid' ,
									'bfromk' => 'bid' ,
									'tok' => 'bid' ,
									'bridge' => 'blog_link' ,
									'model' => 'blog',
	            			),
	            		),
	            	)
        );
            
		///////////////////////////////////////
		// 向系统添加控制器
		$this->application()->accessRouter()->addController("oc\\ext\\blog\\Index",'blog','') ;
		$this->application()->accessRouter()->addController("oc\\ext\\blog\\Insert",'insert') ;
		$this->application()->accessRouter()->addController("oc\\ext\\blog\\Update",'update') ;
		$this->application()->accessRouter()->addController("oc\\ext\\blog\\Delete",'delete') ;
		$this->application()->accessRouter()->addController("oc\\ext\\blog\\FriendBlog",'friendblog',"blog") ;
		
		$this->application()->accessRouter()->addController("oc\\ext\\blog\\TagUpdate",'tag.update') ;
		$this->application()->accessRouter()->addController("oc\\ext\\blog\\TagList",'tag.list') ;
		$this->application()->accessRouter()->addController("oc\\ext\\blog\\TagContent",'tag.content') ;
		$this->application()->accessRouter()->addController("oc\\ext\\blog\\TagHot",'tag.hot') ;
	}
	
}

?>