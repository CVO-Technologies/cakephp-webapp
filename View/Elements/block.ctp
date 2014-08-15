<?php
if (isset($content)):
	$this->startIfEmpty($name);
	echo $content;
	$this->end();
endif;
?>
<x-block data-block="<?php echo $name; ?>" data-block-empty="<?php echo ((isset($empty)) ? $empty : true)  ? 'true' : 'false'; ?>">
	<?php echo $this->fetch($name); ?>
</x-block>
