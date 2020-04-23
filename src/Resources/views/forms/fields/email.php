<?php

$cssClasses = ['form-control','form-control-lg'];
if ($errors) {
    $cssClasses[] = 'is-invalid';
}

?>
<div class="form-group">
    <?php if ($label): ?>
        <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <?php endif; ?>
    <input 
        type="email"
        class="<?php echo implode(' ', $cssClasses); ?>"
        id="<?php echo $id; ?>"
        name="<?php echo $name; ?>"
        <?php if ($helpText): ?>
            aria-describedby="<?php echo $id; ?>Help"
        <?php endif; ?>
        value="<?php echo esc_attr($fieldValue); ?>"
        <?php if ($required): ?>
            required
        <?php endif; ?>
    />
    <?php if ($helpText): ?>
        <small id="<?php echo $id; ?>Help" class="form-text text-muted"><?php echo $helpText; ?></small>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="invalid-feedback">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error['message']; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>