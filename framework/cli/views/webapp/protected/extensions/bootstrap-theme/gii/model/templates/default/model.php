<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 */
?>
<?php echo "<?php\n"; ?>

/**
 * This is the model class for table "<?php echo $tableName; ?>".
 *
 * The followings are the available columns in table '<?php echo $tableName; ?>':
<?php foreach($columns as $column): ?>
 * @property <?php echo $column->type.' $'.$column->name."\n"; ?>
<?php endforeach; ?>
<?php if(!empty($relations)): ?>
 *
 * The followings are the available model relations:
<?php foreach($relations as $name=>$relation): ?>
 * @property <?php
	if (preg_match("~^array\(self::([^,]+), '([^']+)', '([^']+)'\)$~", $relation, $matches))
    {
        $relationType = $matches[1];
        $relationModel = $matches[2];

        switch($relationType){
            case 'HAS_ONE':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'BELONGS_TO':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'HAS_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            case 'MANY_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            default:
                echo 'mixed $'.$name."\n";
        }
	}
    ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?php echo $modelClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return <?php echo $modelClass; ?> the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '<?php echo $tableName; ?>';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		<?php 	foreach($rules as $rule)
			{
				if(strstr($rule,'dropdownfield')=='' &&
					strstr($rule,'autocompletefield')=='' &&
					strstr($rule,'datefield')=='' )
					echo $rule.",\n"; 
			}
			
			?>
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('<?php echo implode(', ', array_keys($columns)); ?>', 'safe', 'on'=>'search'),
		);
	}
	
	
	public function extendedRules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		<?php 	foreach($rules as $rule)
			{
				if(strstr($rule,'dropdownfield')!='' ||
					strstr($rule,'autocompletefield')!='' ||
					strstr($rule,'datefield')!='' )
					echo $rule.",\n"; 
			}
			
			?>
			
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
<?php foreach($relations as $name=>$relation): ?>
			<?php echo "'$name' => $relation,\n"; ?>
<?php endforeach; ?>
		);
	}
	
	
	/**
	*
	*/
	public function attributeIsDirectRelation($attr)
	{
		$relations =$this->relations();
		foreach($relations as $nombre=>$relacion)
			if($relacion[2]===$attr && $relacion[0]==self::BELONGS_TO)
				return true;
		
		return false;
	
	}
	
	/**
	*
	**/
	public function attributeDatatypeRelation($attr)
	{
		$relations =$this->relations();
		foreach($relations as $nombre=>$relacion)
			if($relacion[2]===$attr)
				return $relacion[1];
		
		return null;
	}
	
	
	/**
	* elimina en cascada
	**/
	public function deleteCascade()
	{
<?php 
		//if(!empty($relations)
		foreach($relations as $name=>$relation){
			if (preg_match("~^array\(self::([^,]+), '([^']+)', '([^']+)'\)$~", $relation, $matches))
			{
				
				
				$relationType = $matches[1];
				$relationModel = $matches[2];
				$rname=$name.'n';
				switch($relationType){
					case 'HAS_MANY':
						echo "\t\tforeach (\$this->\$$name as \$$rname )\n\t\t\t\$$rname"."->deleteCascade();\n\n";
					break;
					case 'HAS_ONE':
						echo "\t\t\$$rname"."->deleteCascade();\n\n";
					break;
				}
			}
		} 
		echo "\t\t\$this->delete();\n";
		?>
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
<?php foreach($labels as $name=>$label): ?>
			<?php echo "'$name' => '$label',\n"; ?>
<?php endforeach; ?>
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

<?php
foreach($columns as $name=>$column)
{
	if($column->type==='string')
	{
		echo "\t\t\$criteria->compare('$name',\$this->$name,true);\n";
	}
	else
	{
		echo "\t\t\$criteria->compare('$name',\$this->$name);\n";
	}
}
?>

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
