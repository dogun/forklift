<?php
$dir = __DIR__;
$repoPath = $dir;
$output = [];
$returnCode = 0;
exec("git -C $repoPath pull origin master", $output, $returnCode);
if ($returnCode === 0) {
    echo "Git pull 成功";
} else {
    echo "Git pull 失败，错误信息：" . implode("\n", $output);
}
?>