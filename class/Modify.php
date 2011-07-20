<?php
namespace oc\ext\blog ;


use oc\base\FrontFrame;

use jc\session\Session;
use jc\auth\IdManager;
use jc\auth\Id;
use jc\db\ExecuteException;
use oc\mvc\controller\Controller ;
use oc\mvc\model\db\Model;
use jc\mvc\model\db\orm\PrototypeAssociationMap;
use jc\verifier\Email;
use jc\verifier\Length;
use jc\verifier\NotEmpty;
use jc\mvc\view\widget\Text;
use jc\mvc\view\widget\Select;
use jc\mvc\view\widget\CheckBtn;
use jc\mvc\view\widget\RadioGroup;
use jc\message\Message ;
use jc\mvc\view\DataExchanger ;


/**
 * Enter description here ...
 * @author gaojun
 *
 */
class Update extends Controller
{
	protected function init()
	{
		
		
		
		//创建视图
		$this->createView("defaultView", "Blog.Update.html",true) ;
		
		// 为视图创建控件
		$this->defaultView->addWidget( new Text("title","标题","",Text::single), 'title' )->addVerifier( NotEmpty::singleton (), "请说点什么" ) ;
		$this->defaultView->addWidget( new Text("text","内容","",Text::multiple), 'text' )->addVerifier( NotEmpty::singleton (), "请说点什么" ) ;
		
		//设置model
		//$this->defaultView->setModel( Model::fromFragment('blog') ) ;
		$this->defaultView->setModel( new ModelBlog() ) ;
		
	}
	
	public function process()
	{
		$this->defaultView->model()->load($this->aParams->get("id"),"bid");
		
		$child = $this->defaultView->model()->child('tag');
		foreach ($child->childIterator() as $row){
			$aTag[] = $row["title"];
		}
		$sTag = @implode(" ", $aTag);
		
		$this->defaultView->exchangeData(DataExchanger::MODEL_TO_WIDGET) ;
		$this->defaultView->addWidget( new Text("tag","标签",$sTag,Text::single), 'tag.title' )->addVerifier( NotEmpty::singleton (), "请说点什么" ) ;
		
		
		if( $this->defaultView->isSubmit( $this->aParams ) )		 
		{
            // 加载 视图窗体的数据
            $this->defaultView->loadWidgets( $this->aParams ) ;
            
            // 校验 视图窗体的数据
            if( $this->defaultView->verifyWidgets() )
            {
            	$this->defaultView->exchangeData(DataExchanger::WIDGET_TO_MODEL) ;
            	
            	
            	$this->defaultView->model()->setData('uid',IdManager::fromSession()->currentId()->userId()) ;
            	$this->defaultView->model()->setData('time',time()) ;
            	
            	
            	$this->defaultView->model()->child('tag')->delete() ;
            	$this->defaultView->model()->child('tag')->clearChildren() ;
            	
            	$aTag = explode(" ", $this->aParams->get("tag"));
            	for($i = 0; $i < sizeof($aTag); $i++){
					$this->defaultView->model()->child('tag')->buildChild($aTag[$i],"title");
				}
            	
            	try {
            		if( $this->defaultView->model()->save() )
            		{
	            		$this->defaultView->createMessage( Message::success, "修改成功！" ) ;
	            		$this->defaultView->hideForm() ;
            		}
            		
            		else 
            		{
	            		$this->defaultView->createMessage( Message::success, "修改成功！" ) ;
            		}
            			
            	} catch (ExecuteException $e) {
            			throw $e ;
            	}
           	}
		}
	}
}

?>