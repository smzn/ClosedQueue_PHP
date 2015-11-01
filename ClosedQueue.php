<?php 
class ClosedQueue {

	private $KK = 0.145722117443322;
	private $a1, $b1, $c1; 	//変数
	private $d, $a; 	//2次元配列
	private $p, $b; 	//1次元配列
	
	private $alpha, $mu, $lambda, $L, $R;	//1次元配列
	private $N, $NK, $K; //変数
	//ネットワーク内の総客数がn人の時のノートk゙でのスループット,平均ノード内客数,平均ノード通過時間(滞在時間)とする

	function __construct($a1, $b1, $c1, $d, $p, $n, $k, $mu) {
		$this->a1 = $a1;
		$this->b1 = $b1;
		$this->c1 = $c1;
		$this->d = $d;
		$this->p = $p;
		$this->mu =$mu;
		$this->N = $n;
		$this->NK = $k-1;//ガウスで利用：拠点数-1で実施
		$this->K = $k;
		//lambda = new double[K];
		//L = new double[K];
		//R = new double[K];
		//for(int i = 0; i < L.length; i++) L[i] = 0;
	}

	public function calcGravity(){
		//$N = $this->N-1; 
		
		for($i = 0; $i < count($this->p); $i++){
			for($j = 0; $j < count($this->p); $j++){
				$f[$i][$j] = $this->KK*pow($this->p[$i],$this->a1)*pow($this->p[$j],$this->b1)/pow($this->d[$i][$j],$this->c1);
			}
		}
		//行和を１に正規化
		for($i = 0; $i < count($this->p); $i++){
			$sum = 0;
			for($j = 0; $j < count($this->p); $j++){
				$sum += $f[$i][$j];
			}
			for($j = 0; $j < count($this->p); $j++){
				$f[$i][$j] /= $sum;
			}
		}
		return $f;	
	}

	public function setA($a) {
		$this->a = $a;
	}

	public function setB($b) {
		$this->b = $b;
	}

	public function calcGauss(){
		//$p,$pmax,$s,$w;//w:1次元配列

		/* 前進消去（ピボット選択）*/
		for($k = 0; $k < $this->NK-1; $k++){  /* 第ｋステップ */
		      $p = $k;
		      $pmax = abs( $this->a[$k][$k] );
		      for($i = $k+1; $i < $this->NK; $i++){  /* ピボット選択 */
		         if(abs( $this->a[$i][$k] ) > $pmax){
		            $p = $i;
		            $pmax = abs( $this->a[$i][$k] );
		         }
		      }

		      if($p != $k){  /* 第ｋ行と第ｐ行の交換　*/
		         for($i = $k; $i <$this-> NK; $i++){
		            /* 係数行列　*/
		            $s = $this->a[$k][$i];
		            $this->a[$k][$i] = $this->a[$p][$i];
		            $this->a[$p][$i] = $s;
		         }
		         /* 既知ベクトル */
		         $s = $this->b[$k];
		         $this->b[$k] = $this->b[$p];
		         $this->b[$p] = $s;
		      }
		/* 前進消去 */
		      for($i = $k +1; $i < $this->NK; $i++){ /* 第ｉ行 */
		         $w[$i] = $this->a[$i][$k] / $this->a[$k][$k];
		         $this->a[$i][$k] = 0.0;
		         /* 第ｋ行を-a[i][k]/a[k][k]倍して、第ｉ行に加える */
		         for($j = $k + 1; $j < $this->NK; $j++){
		            $this->a[$i][$j] = $this->a[$i][$j] - $this->a[$k][$j] * $w[$i];
		         }
		         $this->b[$i] = $this->b[$i] - $this->b[$k] * $w[$i];
		      }
		   }
		/* 後退代入 */
		      for($i = $this->NK - 1; $i >= 0; $i--){
		         for($j = $i + 1; $j < $this->NK; $j++){
		            $this->b[$i] = $this->b[$i] - $this->a[$i][$j] * $this->b[$j];
		            $this->a[$i][$j] = 0.0;
		         }
		         $this->b[$i] = $this->b[$i] / $this->a[$i][$i];
		         $this->a[$i][$i] = 1.0;
		      }
		
		return $this->b;
	}

	public function setAlpha($alpha) {
		$this->alpha = $alpha;
	}

	public function calcAverage(){
		$n = 0;
		while($n < $this->N){
			$n++;
			//Step3 Rの更新
			for($i = 0; $i < $this->K;$i++){
				$this->R[$i] = ($this->L[$i] + 1)/$this->mu[$i];
			}
			
			//Step4 Lambdaの更新
			for($i = 0; $i < $this->K;$i++){
				$sum = 0;
				for($j = 0; $j < $this->K; $j++) $sum += $this->alpha[$j]*$this->R[$j]; 
				if($i == 0) $this->lambda[$i] = $n/$sum;
				else $this->lambda[$i] = $this->alpha[$i]*$this->lambda[0];
			}
			
			//Step5 Lの更新
			for($i = 0; $i < $this->K; $i++){
				$this->L[$i] = $this->lambda[$i]*$this->R[$i];
			}
		}
	}

	public function getLambda() {
		return $this->lambda;
	}

	public function getL() {
		return $this->L;
	}

	public function getR() {
		return $this->R;
	}

}
?>
