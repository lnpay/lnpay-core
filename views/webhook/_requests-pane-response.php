<div class="well">
    <pre><code><?=\yii\helpers\HtmlPurifier::process(\yii\helpers\HtmlPurifier::process(htmlspecialchars($iwhr->response_body)));?></code></pre>
</div>