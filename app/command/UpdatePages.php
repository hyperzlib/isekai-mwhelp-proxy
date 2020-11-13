<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

use app\model\HelpPage;
use utils\Nav;

class UpdatePages extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('update-pages')
            ->setDescription('Update page caches');
    }

    protected function execute(Input $input, Output $output)
    {
        $nav = new Nav();
        $output->writeln('Staring update pages...');
        foreach($nav->navTree as $item){
            $page = $item['page'];
            if(in_array($page, ['Index'])) continue;
            $output->write('Updating ' .  $page . ' ... ');
            try {
                $result = HelpPage::getPage($page);
                if($result){
                    $output->writeln('Success.');
                }
            } catch(Exception $ex){
                $output->writeln('Failed: ' . $ex->getMessage());
            }
        }
    }
}
