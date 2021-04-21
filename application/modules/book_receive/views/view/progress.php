<?php

$progress_list = ['handover', 'wrapping'];

function get_book_receive_progress($progress = null, $book_receive, $progress_list)
{
    if (!in_array($progress, $progress_list)) {
        return [
            'class' => '',
            'title' => '',
        ];
    }

    ${"{$progress}_class"} = '';
    ${"{$progress}_title"} = '';
    if ($book_receive->{"is_{$progress}"}) {
        ${"{$progress}_class"} .= 'success ';
        ${"{$progress}_title"} = 'Selesai';
    } 
    else if (format_datetime($book_receive->{"{$progress}_start_date"})) {
        ${"{$progress}_class"} .= 'active ';
        ${"{$progress}_title"} = 'Dalam Proses';
    } 
    else {
        ${"{$progress}_title"} = 'Belum mulai';
    }

    if ($progress == 'handover') {
        $text = 'serah terima';
    } elseif ($progress == 'wrapping') {
        $text = 'wrapping';
    } else {
        $text = '';
    }

    return [
        'class' => ${"{$progress}_class"},
        'title' => ${"{$progress}_title"},
        'text' => $text
    ];
}

?>

<section id="progress-list-wrapper" class="card">
    <div id="progress-list">
        <header class="card-header">Progress</header>
        <div class="card-body">
            <ol class="progress-list mb-0 mb-sm-4">
                <?php  foreach ($progress_list as $progress) : ?>
                <?php $progress_data = get_book_receive_progress($progress, $book_receive, $progress_list) ?>
                <li class="<?= $progress_data['class'] ?>">
                    <button data-html="true" type="button" data-toggle="tooltip" title="<?= $progress_data['title'] ?>">
                        <span width="300px" class="progress-indicator"></span>
                    </button>
                    <span class="progress-label d-none d-sm-inline-block"><?= $progress_data['text']; ?></span>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</section>

