<div class="clearfix">
    <label for="rag" id="rag_title">Select RAG</label>

    <div class="input pad">
        <select name="rag" id="rag">
            <?php // get the RAG statuses in the system

            $statuses = $DB->get_records('block_ilp_plu_sts_items');
            echo '<option>--Select--</option>';
            foreach ($statuses as $item) {
                echo '<option value="', $item->id, '">', $item->name, '</option>';
            }
            ?>
        </select>
    </div>
</div>