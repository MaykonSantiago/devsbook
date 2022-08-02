<?= $render('header', ['loggedUser' => $loggedUser, 'user' => $user, 'flash' => $flash]); ?>
<section class="container main">
    <?= $render('sidebar', ['activeMenu' => 'config']); ?>
    <section class="feed mt-10">
        <div class="row">
            <div class="column pr-5">
                <h1>Configurações</h1><br /><br />
                <label>Novo Avatar</label>
                <input type="file" name="avatar" /><br />
                <label>Nova Capa</label>
                <input type="file" name="cover" /><br />
                <hr />
                <form class="form-config" method="POST" action="<?= $base; ?>/configuracoes">
                    <?php if (!empty($flash) && !strpos($flash, 'senha')) : ?>
                        <div class="flash"><?php echo $flash; ?></div>
                    <?php endif; ?>

                    <input placeholder="<?= $user->id ?>" type="hidden" name="id" />
                    <br />

                    <label>Nome Completo:</label><br />
                    <input class="config-new-input " placeholder="<?= $user->name ?>" type="text" name="name" /><br /><br />

                    <label>Data de Nascimento:</label><br />
                    <input class="config-new-input " id="birthdate" placeholder="<?= date('d/m/Y', strtotime($user->birthdate)); ?>" type="text" name="birthdate" /><br /><br />
                    
                    <label>E-mail:</label><br />
                    <input class="config-new-input " value="" placeholder="<?= $user->email ?>" type="email" name="email" /><br /><br />
                    
                    <?php if (($user->city) != "") : ?>
                        <label>Cidade:</label><br />
                        <input class="config-new-input " placeholder="<?= $user->city ?>" type="text" name="city" /><br /><br />
                    <?php else : ?>
                        <label>Cidade:</label><br />
                        <input class="config-new-input " placeholder="Qual a sua cidade?" type="text" name="city" /><br /><br />
                    <?php endif; ?>

                    <?php if (($user->work) != "") : ?>
                        <label>Trabalho:</label><br />
                        <input class="config-new-input " placeholder="<?= $user->work ?>" type="text" name="work" /><br /><br />
                        <hr /><br />
                    <?php else : ?>
                        <label>Trabalho:</label><br />
                        <input class="config-new-input " placeholder="Onde você trabalha?" type="text" name="work" /><br /><br />
                        <hr /><br />
                    <?php endif; ?>
                    
                    <?php if (!empty($flash) && strpos($flash, 'senha')) : ?>
                        <div class="flash"><?php echo $flash; ?></div>
                    <?php endif; ?>
                    <label>Nova senha:</label><br />
                    <input class="config-new-input " id="newPassword" placeholder="Caso queira alterar sua senha, digite a nova senha." type="password" name="newPassword" /><br /><br />
                    
                    <label>Confirmar senha:</label><br />
                    <input class="config-new-input " id="confirmPassword" placeholder="Repita a senha para confirmar." type="password" name="confirmPassword" /><br /><br />
                    
                    <input class="button" type="submit" value="Salvar" />
                </form>
            </div>
            <div class="column side pl-5">
                <?= $render('right-side'); ?>
            </div>
        </div>
    </section>
</section> 

<script src="https://unpkg.com/imask"></script>
<script>
    IMask(
        document.getElementById('birthdate'), {
            mask: '00/00/0000'
        }
    );
</script>
<?= $render('footer'); ?>