<?php
App::uses('AppController', 'Controller');
/**
 * Facilities Controller
 *
 * @property Facility $Facility
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class FacilitiesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Facility->recursive = 0;
		$this->paginate = array(
                        'limit'=>100,
                );
		$this->set('facilities', $this->Paginator->paginate());
	}

	public function choice() {
                $this->Facility->recursive = 0;
                $this->paginate = array(
                        'conditions'=>array(
                                'OR' => array(
                                                //array('firehouse_id LIKE' => '_______'),
                                                //array('hospital_id LIKE' => '__________')
                                )
                        ),
                        'order' => array('id' => 'asc'),
                        'limit'=>100,
                );

                $this->set('facilities', $this->Paginator->paginate());
        }


	public function closedqueue() {

		App::import('Model','Parameter');
		$this->Parameter = new Parameter;
		$parameters = $this->Parameter->find('all',
			array(
            			'fields' => array('fromid','toid','distance'),
			)
		);

		//重力モデル用の配列作成
		foreach($parameters as $parameter){
			$d[$parameter['Parameter']['fromid']-1][$parameter['Parameter']['toid']-1] = $parameter['Parameter']['distance'];
			$d[$parameter['Parameter']['toid']-1][$parameter['Parameter']['fromid']-1] = $parameter['Parameter']['distance'];
		}
		$count = $this->Facility->find('count');
		for($i = 0; $i < $count; $i++){
			$d[$i][$i] = 10000000;
		}

		$facilities = $this->Facility->find('all',
			array(
                                'fields' => array('id','popularity','servicerate'),
                        )
                );

		foreach($facilities as $facility){
			$p[$facility['Facility']['id']-1] = $facility['Facility']['popularity'];
                }
		$index = 0;
		foreach($facilities as $facility){
			$mu[$index++] = $facility['Facility']['servicerate'];
                }

		App::import('Vendor','ClosedQueue');
		$closedqueue = new ClosedQueue(1,1,1,$d,$p,100,42,$mu);
		$f = $closedqueue->calcGravity();
		
		$this->set('gravity', $f);

		for($i = 0; $i < $count -1; $i++){
			for($j = 0; $j < $count-1; $j++){
				if( $i == $j ) {
					$ff[$i][$j] = $f[$j + 1][$i + 1] - 1; 
				}else {
					$ff[$i][$j] = $f[$j + 1][$i + 1];
				}
			}
		}
		for($i = 0;$i < $count -1; $i++){
			$bb[$i] = -$f[0][$i+1];
		}
		$this->set('ff',$ff);
		$this->set('bb',$bb);

		//alphaを求める
		$closedqueue->setA($ff);
		$closedqueue->setB($bb);
		$alpha = $closedqueue->calcGauss();

		//alphaの配列の大きさが-1になってしまうので、元の大きさのalpha1に入れ直す
		for($i = 0 ; $i < $count; $i++){
			if( $i == 0) $alpha1[$i] = 1;
			else $alpha1[$i] = $alpha[$i-1];
		}
		$this->set('alpha',$alpha1);
	
		$closedqueue->setAlpha($alpha1);
		$closedqueue->calcAverage();

		$L = $closedqueue->getL();
		$R = $closedqueue->getR();
		$lambda = $closedqueue->getLambda();

		$this->set('L',$L);
		$this->set('R',$R);
		$this->set('lambda',$lambda);
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Facility->exists($id)) {
			throw new NotFoundException(__('Invalid facility'));
		}
		$options = array('conditions' => array('Facility.' . $this->Facility->primaryKey => $id));
		$this->set('facility', $this->Facility->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Facility->create();

			$elevation = $this->getElv($this->request->data['Facility']['latitude'],$this->request->data['Facility']['longitude']);
      			$this->request->data['Facility']['elevation'] = $elevation;

			if ($this->Facility->save($this->request->data)) {
				$this->Session->setFlash(__('The facility has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The facility could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Facility->exists($id)) {
			throw new NotFoundException(__('Invalid facility'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Facility->save($this->request->data)) {
				$this->Session->setFlash(__('The facility has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The facility could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Facility.' . $this->Facility->primaryKey => $id));
			$this->request->data = $this->Facility->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Facility->id = $id;
		if (!$this->Facility->exists()) {
			throw new NotFoundException(__('Invalid facility'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Facility->delete()) {
			$this->Session->setFlash(__('The facility has been deleted.'));
		} else {
			$this->Session->setFlash(__('The facility could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
