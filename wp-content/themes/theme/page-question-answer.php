<?php
/* 
    Template Name: question-answer
*/
?>

<?php get_header('home'); ?>


<main class="main">

    <?php
    // Получаем все посты CPT 'question_answer'
    $args = array(
        'post_type'      => 'question_answer', // если у вас другой slug — поменяйте
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );

    $faq_query = new WP_Query( $args );

    if ( $faq_query->have_posts() ) :
        // Соберём только те посты, где есть хотя бы одна полная пара question/answer
        $posts_with_pairs = array();

        while ( $faq_query->have_posts() ) : $faq_query->the_post();
            $post_id = get_the_ID();
            $pairs = array();

            for ( $i = 1; $i <= 5; $i++ ) {
                // Попробуем получить через ACF, иначе - через meta
                if ( function_exists( 'get_field' ) ) {
                    $q = get_field( "question_{$i}", $post_id );
                    $a = get_field( "answer_{$i}", $post_id );
                } else {
                    $q = get_post_meta( $post_id, "question_{$i}", true );
                    $a = get_post_meta( $post_id, "answer_{$i}", true );
                }

                $q = is_string($q) ? trim( $q ) : $q;
                $a = is_string($a) ? trim( $a ) : $a;

                // Добавляем пару только если оба поля непустые
                if ( $q !== '' && $q !== null && $a !== '' && $a !== null ) {
                    $pairs[] = array(
                        'question' => $q,
                        'answer'   => $a,
                    );
                }
            }

            if ( count( $pairs ) > 0 ) {
                $posts_with_pairs[] = array(
                    'ID'    => $post_id,
                    'title' => get_the_title( $post_id ),
                    'pairs' => $pairs,
                );
            }
        endwhile;
        wp_reset_postdata();

        // Если есть подходящие посты - выводим разметку
        if ( ! empty( $posts_with_pairs ) ) : ?>
            <section class="question-answer">
                <div class="container">
                    <div class="question-answer__inner">
                        <h2 class="page-title">ВОПРОС — ОТВЕТ</h2>

                        <div class="question-answer__tabs">
                            <?php foreach ( $posts_with_pairs as $index => $post ) : ?>
                                <button class="question-answer__tabs-btn" type="button" data-index="<?php echo esc_attr( $index ); ?>">
                                    <p><?php echo esc_html( $post['title'] ); ?></p>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <div class="question-answer__content">
                            <?php foreach ( $posts_with_pairs as $post ) : ?>
                                <div class="question-answer__content-item">
                                    <?php foreach ( $post['pairs'] as $pair ) : ?>
                                        <div class="question-answer__info">
                                            <div class="question-answer__info-head" role="button" tabindex="0" aria-expanded="false">
                                                <h3 class="question-answer__info-title"><?php echo esc_html( $pair['question'] ); ?></h3>
                                                <img class="question-answer__info-arrow" src="<?php echo esc_attr( get_template_directory_uri() . '/assets/img/to_open_tab.svg' ); ?>" alt="">
                                            </div>
                                            <div class="question-answer__info-text">
                                                <p><?php echo wp_kses_post( wpautop( $pair['answer'] ) ); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>
            </section>
        <?php
        endif;
    endif;
    ?>


    <section class="farm-scene">
        <div class="container">
            <div class="farm-scene__inner">
                <!-- Трактор -->
                <div class="farm-scene__left">
                    <img class="farm-scene__tractor"
                        src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/tractor.svg"
                        alt="Трактор" />
                    <img class="farm-scene__ground"
                        src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/ground.svg" alt="Дорога">
                    <img class="farm-scene__ground-mob"
                        src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/ground_mob.svg"
                        alt="Дорога">
                </div>

                <!-- Мельница: база и лопасти -->
                <div class="farm-scene__mid">
                    <img class="farm-scene__grinder"
                        src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/grinder.svg"
                        alt="Лопасти" />
                    <img class="farm-scene__mill-base"
                        src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/mill.svg"
                        alt="Мельница" />
                </div>

                <!-- Хлеб и корзина -->
                <div class="farm-scene__bread">
                    <img src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/bread.svg" alt="Хлеб" />
                </div>
                <div class="farm-scene__basket">
                    <img src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/cart.svg"
                        alt="Корзина" />
                </div>
            </div>
        </div>
    </section>

</main>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const tabList = document.querySelector('.question-answer__tabs');
    const tabs = document.querySelectorAll('.question-answer__tabs-btn');
    const contents = document.querySelectorAll('.question-answer__content-item');

    // табы 
    if (tabList && tabs.length && contents.length) {
        tabList.setAttribute('role', 'tablist');

        const activateTab = (index) => {
        tabs.forEach((btn, i) => {
            btn.classList.toggle('is-active', i === index);
            btn.setAttribute('aria-selected', i === index ? 'true' : 'false');
            btn.setAttribute('tabindex', i === index ? '0' : '-1');
        });
        contents.forEach((block, i) => {
            // показываем только выбранный контент
            block.style.display = i === index ? '' : 'none';
        });
        // при переключении табов обновляем открытые панели внутри видимого блока
        document.querySelectorAll('.question-answer__info.is-open .question-answer__info-text').forEach(p => {
            p.style.maxHeight = p.scrollHeight + 'px';
        });
        };

        // инициализация
        activateTab(0);

        tabs.forEach((btn, i) => {
        btn.setAttribute('role', 'tab');
        btn.addEventListener('click', () => activateTab(i));
        // enter/space
        btn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            activateTab(i);
            }
        });
        });
    }

    const heads = document.querySelectorAll('.question-answer__info');

    heads.forEach(head => {
        const parent = head.closest('.question-answer__info');
        if (!parent) return;
        const panel = parent.querySelector('.question-answer__info-text');

        // подготовка стилей
        if (panel) {
        panel.style.overflow = 'hidden';
        panel.style.transition = 'max-height 0.25s ease';
        // при загрузке — свернуть
        panel.style.maxHeight = parent.classList.contains('is-open') ? panel.scrollHeight + 'px' : '0px';
        }

        head.setAttribute('role', 'button');
        head.setAttribute('tabindex', '0');
        head.style.cursor = 'pointer';

        const toggle = () => {
        const isOpen = parent.classList.contains('is-open');
        if (!panel) return;
        if (isOpen) {
            parent.classList.remove('is-open');
            panel.style.maxHeight = '0px';
            head.setAttribute('aria-expanded', 'false');
        } else {
            parent.classList.add('is-open');
            // нужно временно поставить display:block если он был none, чтобы корректно считать scrollHeight
            const prevDisplay = panel.style.display;
            if (getComputedStyle(panel).display === 'none') panel.style.display = 'block';
            panel.style.maxHeight = panel.scrollHeight + 'px';
            // вернуть display если было явно задано
            panel.style.display = prevDisplay || '';
            head.setAttribute('aria-expanded', 'true');
        }
        };

        head.addEventListener('click', toggle);
        head.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggle();
        }
        });
    });

    // При ресайзе пересчитываем открытые панели 
    window.addEventListener('resize', () => {
        document.querySelectorAll('.question-answer__info.is-open .question-answer__info-text').forEach(p => {
        p.style.maxHeight = p.scrollHeight + 'px';
        });
    });
    });

</script>
<?php get_footer('home'); ?>