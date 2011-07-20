<?php
namespace oc\ext\blog ;

use oc\base\FrontFrame;
use jc\session\Session;
use jc\auth\IdManager;
use jc\auth\Id;
use jc\db\ExecuteException;
use oc\mvc\controller\Controller ;
use jc\verifier\Email;
use jc\verifier\Length;
use jc\verifier\NotEmpty;
use jc\mvc\view\widget\Text;
use jc\mvc\view\widget\Select;
use jc\mvc\view\widget\CheckBtn;
use jc\mvc\view\widget\RadioGroup;
use jc\message\Message ;
use jc\mvc\view\DataExchanger ;
use oc\mvc\model\db\Model;


/**
 * Enter description here ...
 * @author gaojun
 *
 */
class Insert extends Controller
{
	protected function init()
	{
		
		//创建视图
		$this->createView("Insert", "Blog.Insert.html",true) ;
		
		// 为视图创建控件
		$this->viewInsert->addWidget( new Text("title","标题","",Text::single), 'title' )->addVerifier( NotEmpty::singleton (), "请说点什么" ) ;
		$this->viewInsert->addWidget( new Text("text","内容","",Text::multiple), 'text' )->addVerifier( NotEmpty::singleton (), "请说点什么" ) ;
		$this->viewInsert->addWidget( new Text("tag","标签","",Text::single), 'tag.title' )->addVerifier( NotEmpty::singleton (), "请说点什么" ) ;
		
		//设置model
		$this->viewInsert->setModel( new ModelBlog() ) ;
		//$this->viewInsert->setModel( Model::fromFragment('blog',array("tag")) ) ;
		
	}
	
	public function process()
	{
		
		if( $this->viewInsert->isSubmit( $this->aParams ) )
		{
            // 加载 视图窗体的数据
            $this->viewInsert->loadWidgets( $this->aParams ) ;
            
            // 校验 视图窗体的数据
            if( $this->viewInsert->verifyWidgets() )
            {
            	$this->viewInsert->exchangeData(DataExchanger::WIDGET_TO_MODEL) ;
            	
            	
				$this->viewInsert->model()->setData('uid',IdManager::fromSession()->currentId()->userId()) ;
				$this->viewInsert->model()->setData('time',time()) ;
				
				$aTag = explode(" ", $this->aParams->get("tag"));
				
				for($i = 0; $i < sizeof($aTag); $i++){
					$this->viewInsert->model()->child('tag')->buildChild($aTag[$i],"title");
				}
				
            	try {
            		if( $this->viewInsert->model()->save() )
            		{
            			$this->viewInsert->createMessage( Message::success, "发布成功！" ) ;
            			$this->viewInsert->hideForm() ;
            		}
            		else 
            		{
            			$this->viewInsert->createMessage( Message::failed, "遇到错误！" ) ;
            		}
            		
            			
            	} catch (ExecuteException $e) {
            			throw $e ;
            	}
           	}
		}
	}
}

?>