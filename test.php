
                <?
                    $i = 0;
                    $j = 1;
                    foreach ($this->ResData as $row)
                    {
                        $start++;
                        $i++;


                        if ($row['is_spcl'] == 1)
                        {
                            $image = 'tick.png';
                        }

                        if ($row['is_spcl'] == 0)
                        {
                            $image = 'cross.png';
                        }

                        if ($row['is_popular'] == 1)
                        {
                            $image1 = 'tick.png';
                        }
                        if ($row['is_popular'] == 0)
                        {
                            $image1 = 'cross.png';
                        }


                        switch ($row['status'])
                        {
                            case '1': $status1 = 'selected="selected"';
                                $status2 = "";
                                $status3 = "";
                                $status4 = "";
                                break;
                            case '2': $status1 = '';
                                $status2 = 'selected="selected"';
                                $status3 = "";
                                $status4 = "";
                                break;
                            case '3': $status1 = '';
                                $status2 = "";
                                $status3 = 'selected="selected"';
                                $status4 = "";
                                break;
                            case '4': $status1 = '';
                                $status2 = "";
                                $status3 = "";
                                $status4 = 'selected="selected"';
                                break;
                        }

                        if ($cClass == "gradeX odd")
                        {
                            $cClass = "gradeX even";
                        }
                        else
                        {
                            $cClass = "gradeX odd";
                        }
                        ?>                    
                        <tr class="gradeX">
                            <td style="text-align:center"><?= $i; ?></td>
                            <td style="text-align:center">
                                <input name='check<?= $j; ?>' id='check<?= $j; ?>' value='<?= $row['id']; ?>' 
                                       onchange='return check_check("bcdel","deletebcchk")' type='checkbox'>

                                <script> 
                                $('#bcdel').html(''); document.getElementById('deletebcchk').checked = false;
                                </script>
                            </td>
                            <td><?= $row['propertycode'] ?></td>
                            <?php
                            if ($this->inhouse)
                            {
                                ?>
                                <td><?= !empty($row['subscriber_name']) ? $row['subscriber_name'] : '--NA--' ?></td>
                                <td><?= !empty($row['xml_property_id']) ? $row['xml_property_id'] : '--NA--' ?></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td><?= $row['first_name'] . " " . $row['last_name'] ?></td>

                                <?php
                            }
                            ?>
                            <td><?= $row['ptyle_name'] ?></td>                            
                            <td><?= date('d-m-Y', strtotime($row['date_added'])) ?></td>                            
                            <td>
                                <select class="chzn-select" name = "status<?= $i ?>" id = "status<?= $i ?>" onchange='changestatus("<?= $row['id']; ?>",this.value)'>
                                    <option <?= $status1 ?> value="1">Incomplete</option>
                                    <option <?= $status2 ?> value="2">Pending Approval</option>                                    
                                    <option <?= $status3 ?> value="3">Live</option>                                    
                                    <option <?= $status4 ?> value="4">Suspended</option>                                    
                                </select>
                            </td>
                          <!--  <td><input type="checkbox" name="suspended<?= $i ?>" <? if($row['status'] == '4') { echo "checked";}else{ echo "";}?>  onclick='changestatus("<?= $row['id']; ?>","4")'></td>   --> 
                            <td>
                                <a href="<?= APPLICATION_URL_ADMIN ?>property/editproperty/pptyId/<?= $row['id'] ?>/step/0"><img src="<?= APPLICATION_URL_ADMIN; ?>css/icons/32/application_form_edit.png" height="16" width="16"   border='0' title='Edit' alt='Edit'></a>
                            </td> 
                        </tr>
                        <?
                        $j++;
                    }
                ?>
            