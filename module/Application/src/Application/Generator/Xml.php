<?php

namespace Application\Generator;

use Application\Entity\User;
use Application\HashString;
use Zend\Stdlib\Parameters;

class Xml
{

    public function generateUpdateUser($users)
    {
        $writer = new \XMLWriter('UTF-8');
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString(str_repeat(' ', 4));

        $writer->startDocument("1.0", "utf-8", "no");
        {
            $writer->startElement("Users");
            {
				foreach($users as $id=>$user){
					$writer->startElement("User");
					$writer->writeAttribute('ID',$id);
					foreach($user as $val=>$data){
						$writer->writeElement($val, $data);
					}
				$writer->endElement();
				}	
			}
            $writer->endElement();
        }
        $writer->endDocument();

        return $writer->flush();
    }
	
	public function generateAuthorization(User $user, \Application\Model\User $model)
    {
        $writer = new \XMLWriter('UTF-8');
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString(str_repeat(' ', 4));

        $writer->startDocument("1.0", "utf-8", "no");
        {
            $writer->startElement("Authorization");
            {
                $writer->writeElement("Login", $user->getLogin());
                $writer->writeElement("Password", $model->hashPassword($user));
            }
            $writer->endElement();
        }
        $writer->endDocument();

        return $writer->flush();
    }

    public function generateProjectViewForGip(User $user, $cipher)
    {
        $writer = new \XMLWriter('UTF-8');
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString(str_repeat(' ', 4));

        $writer->startDocument("1.0", "utf-8", "no");
        {
            $writer->startElement("Designer");
            {
                $writer->writeElement("IP_Server", '127.0.0.1');
                $writer->writeElement("Cipher", $cipher);
                $writer->writeElement("ID_Designer", $user->getId());
                $writer->writeElement("IP_Designer", $user->getIp());
                $writer->writeElement("FIO_Designer", 'fake');
            }
            $writer->endElement();
        }
        $writer->endDocument();
        return $writer->flush();
    }

    public function generateProjectViewForDesigner(User $user, $cipher)
    {
        $writer = new \XMLWriter('UTF-8');
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString(str_repeat(' ', 4));

        $writer->startDocument("1.0", "utf-8", "no");
        {
            $writer->startElement("Designer");
            {
                $writer->writeElement("IP_Server", '127.0.0.1');
                $writer->writeElement("Cipher", $cipher);
                $writer->writeElement("ID_Designer", $user->getId());
                $writer->writeElement("IP_Designer", $user->getIp());
                $writer->writeElement("FIO_Designer", 'fake');
            }
            $writer->endElement();
        }
        $writer->endDocument();
        return $writer->flush();
    }

    public function generateProjectCreate(Parameters $data ,/* Parameters */ $designers, User $user)
    {
        $writer = new \XMLWriter('UTF-8');
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString(str_repeat(' ', 4));

        $writer->startDocument("1.0", "utf-8", "no");
        {
            $writer->startElement("Project");
            {
			//$writer->writeElement("ObjectName", "$data[cipher]");
                $writer->writeElement("ObjectName", $data->object);
				$writer->writeElement("FIO_GIP", $user->getFio()); 
                $writer->writeElement("ID_GIP", $user->getId());
                $writer->writeElement("Cipher", $data->cipher);
                $writer->writeElement("CreationTime", date('d/m/Y H:m'));
				$writer->writeElement("IP_Server", "127.0.0.1:4545"); 
                $writer->writeElement("NumberProjElements", $data->pe_count);
                $writer->startElement("ListPE");
                {
                    foreach($data->pe as $pe) {
                        $writer->startElement("ID");
                        $writer->writeAttribute("PE", $pe['number']);
                        {
                            $writer->writeElement("ID_Designer", $pe['designer']);
                            $writer->writeElement("KX", !empty($pe['coordinates']['x']) ? $pe['coordinates']['x'] : 0);
                            $writer->writeElement("KY", !empty($pe['coordinates']['y']) ? $pe['coordinates']['y'] : 0);
                            $writer->writeElement("KZ", !empty($pe['coordinates']['z']) ? $pe['coordinates']['z'] : 0);
                            $writer->writeElement("DX", !empty($pe['size']['x']) ? $pe['size']['x'] : 0);
                            $writer->writeElement("DY", !empty($pe['size']['y']) ? $pe['size']['y'] : 0);
                            $writer->writeElement("DZ", !empty($pe['size']['z']) ? $pe['size']['z'] : 0);
							
							$writer->writeElement("IP_PE", $designers[$pe['designer']]->getIp());
							$writer->writeElement("ID_Designer" , $designers[$pe['designer']]->getId());
							$writer->writeElement("FIO_Designer" , $designers[$pe['designer']]->getFio());

                            $writer->writeElement("File", '');

                            $writer->writeElement("Description", $pe['description']);
                        }
                        $writer->endElement();
                    }
                }
                $writer->endElement();

                $writer->startElement("CombinationPE");
                {
                    $links = array();
                    foreach($data->pe as $peNumber => $pe) {
                        $links[$peNumber] = array();
                        if(array_key_exists('link', $pe)) {
                            foreach($pe['link'] as $linkPeNumber => $linkPe) {
                                if(!empty($linkPe['s']) || !empty($linkPe['p']) || !empty($linkPe['o'])) {
                                    $links[$peNumber][$linkPeNumber] = array(
                                        's' => !empty($linkPe['s']) ? $linkPe['s'] : 0,
                                        'p' => !empty($linkPe['p']) ? $linkPe['p'] : 0,
                                        'o' => !empty($linkPe['o']) ? $linkPe['o'] : 0,
                                    );
                                }
                            }
                        }
                    }

                    foreach($links as $peNumber => $pe) {
                        if(!empty($pe)) {
                            $writer->startElement("ID");
                            $writer->writeAttribute("PE", $peNumber);
                            {
                                foreach($pe as $peLinkNumber => $peLink) {
                                    $writer->startElement("PE");
                                    $writer->writeAttribute("PE", $peNumber . '-' . $peLinkNumber);
                                    {
                                        $writer->writeElement("S", $peLink['s']);
                                        $writer->writeElement("P", $peLink['p']);
                                        $writer->writeElement("O", $peLink['o']);
                                    }
                                    $writer->endElement();
                                }
                            }
                            $writer->endElement();
                        }
                    }
                }
                $writer->endElement();

            }
            $writer->endElement();

        }
        $writer->endDocument();

        return $writer->flush();
    }

    public function generateCommonNodes($data)
    {
        $writer = new \XMLWriter('UTF-8');
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString(str_repeat(' ', 4));

        $writer->startDocument("1.0", "utf-8", "no");
        {
            $writer->startElement("report");
            {
                $writer->writeElement("Cipher", $data['cipher']);
                $writer->writeElement("BSW", $data['bps']);
                $writer->writeElement("MePE", $data['common_pe']);

                $writer->startElement("Type");
                {
                    foreach($data['pe'] as $peNumber => $nodes) {
                        $writer->startElement("ID");
                        $writer->writeAttribute('PE', $peNumber);
                        {
                            foreach($nodes as $nodeNumber => $node) {
                                $writer->startElement("N");
                                $writer->writeAttribute('CommNode', $nodeNumber);
                                {
                                    $writer->writeElement("X", $node['x']);
                                    $writer->writeElement("Y", $node['y']);
                                    $writer->writeElement("Z", $node['z']);
                                }
                                $writer->endElement();
                            }
                        }
                        $writer->endElement();
                    }
                }
                $writer->endElement();
            }
            $writer->endElement();
        }
        $writer->endDocument();
        return $writer->flush();
		
    }

    private function getFile($peNumber, Parameters $files)
    {
        if(isset($files->pe[$peNumber]['fileModel']) && $files->pe[$peNumber]['fileModel']['error'] == 0) {
            $file = $files->pe[$peNumber]['fileModel'];
            $fileContent = file_get_contents($file['tmp_name']);
            if($fileContent !== false) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = md5($fileContent) . ($ext ? '.' : '') . $ext;
                $filePath = $this->getRootPath() . '/data/files/' . $fileName;
                if(file_put_contents($filePath, $fileContent) !== false) {
                    return $filePath;
                } else {
                    throw new \ErrorException('Не удалось сохранить файл');
                }
            } else {
                throw new \ErrorException('Не удалось прочитать файл');
            }
        }

        return null;
    }

    private function getRootPath()
    {
        return dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
    }

}