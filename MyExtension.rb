module MyExtension

  def self.extend_horizontal_to_vertical_side
    model = Sketchup.active_model
    selection = model.selection

    # 1. ตรวจสอบการเลือกวัตถุ
    if selection.length != 2
      UI.messagebox("กรุณาเลือกวัตถุ 2 ชิ้น (Group หรือ Component) ตามลำดับ:\n1. แท่งแนวนอน (ที่จะยืด)\n2. แท่งแนวตั้ง (วัตถุเป้าหมาย)")
      return
    end

    object_to_extend = selection[0] # แท่งแนวนอน (วัตถุที่จะยืด)
    target_object = selection[1]    # แท่งแนวตั้ง (วัตถุเป้าหมาย)

    # ตรวจสอบว่าเป็น Group หรือ Component Instance
    unless object_to_extend.is_a?(Sketchup::Group) || object_to_extend.is_a?(Sketchup::ComponentInstance)
      UI.messagebox("ข้อผิดพลาด: วัตถุชิ้นแรกที่เลือกต้องเป็น Group หรือ Component")
      return
    end

    unless target_object.is_a?(Sketchup::Group) || target_object.is_a?(Sketchup::ComponentInstance)
      UI.messagebox("ข้อผิดพลาด: วัตถุชิ้นที่สองที่เลือกต้องเป็น Group หรือ Component")
      return
    end

    model.start_operation("ยืดแท่งแนวนอนไปชนข้างแท่งแนวตั้ง", true)

    begin
      # ดึง Bounding Box ของวัตถุทั้งสอง (ใน Global Coordinates)
      bbox_extend = object_to_extend.bounds # Bounding Box ของแท่งแนวนอน
      bbox_target = target_object.bounds    # Bounding Box ของแท่งแนวตั้ง

      # 3. กำหนดแกนและทิศทางการยืด (X หรือ Y)
      
      # หาจุดกึ่งกลางของวัตถุทั้งสอง
      center_extend = bbox_extend.center
      center_target = bbox_target.center

      extend_axis = nil # แกนที่จะยืด (0=X, 1=Y)
      extend_direction_vector = nil # ทิศทางการยืด (Vector3d)
      current_coord_to_extend_from = nil # พิกัด X หรือ Y ปัจจุบันของปลายที่ต้องการยืด
      target_coordinate = nil # พิกัด X หรือ Y ที่เป็นเป้าหมาย

      # ใช้ Tolerance สำหรับการเปรียบเทียบในแนวตั้ง เพื่อให้วัตถุที่ใกล้เคียงกันถูกพิจารณาว่าอยู่ในแนวเดียวกัน
      tolerance = 0.001 # 1 มม. หรือเล็กกว่านั้น

      # ตรวจสอบว่าวัตถุเป้าหมายอยู่ทาง X หรือ Y ของวัตถุที่จะยืด
      # (พิจารณาจาก bbox และ center.y/x เพื่อให้ครอบคลุมกรณีที่วัตถุเยื้องศูนย์เล็กน้อย)

      # ถ้าแท่งแนวนอนอยู่ทางซ้ายของแท่งแนวตั้ง (ยืดไปทาง +X)
      if (bbox_extend.max.x < bbox_target.min.x) && (bbox_extend.center.y.between?(bbox_target.min.y - tolerance, bbox_target.max.y + tolerance) || bbox_target.center.y.between?(bbox_extend.min.y - tolerance, bbox_extend.max.y + tolerance))
        extend_axis = 0 # X-axis
        extend_direction_vector = Geom::Vector3d.new(1, 0, 0) # ไปทาง +X
        current_coord_to_extend_from = bbox_extend.max.x # ปลาย X สูงสุดของแท่งแนวนอน
        target_coordinate = bbox_target.min.x # ด้าน X ต่ำสุดของแท่งแนวตั้ง
      # ถ้าแท่งแนวนอนอยู่ทางขวาของแท่งแนวตั้ง (ยืดไปทาง -X)
      elsif (bbox_extend.min.x > bbox_target.max.x) && (bbox_extend.center.y.between?(bbox_target.min.y - tolerance, bbox_target.max.y + tolerance) || bbox_target.center.y.between?(bbox_extend.min.y - tolerance, bbox_extend.max.y + tolerance))
        extend_axis = 0 # X-axis
        extend_direction_vector = Geom::Vector3d.new(-1, 0, 0) # ไปทาง -X
        current_coord_to_extend_from = bbox_extend.min.x # ปลาย X ต่ำสุดของแท่งแนวนอน
        target_coordinate = bbox_target.max.x # ด้าน X สูงสุดของแท่งแนวตั้ง
      # ถ้าแท่งแนวนอนอยู่ข้างหลังของแท่งแนวตั้ง (ยืดไปทาง +Y)
      elsif (bbox_extend.max.y < bbox_target.min.y) && (bbox_extend.center.x.between?(bbox_target.min.x - tolerance, bbox_target.max.x + tolerance) || bbox_target.center.x.between?(bbox_extend.min.x - tolerance, bbox_extend.max.x + tolerance))
        extend_axis = 1 # Y-axis
        extend_direction_vector = Geom::Vector3d.new(0, 1, 0) # ไปทาง +Y
        current_coord_to_extend_from = bbox_extend.max.y # ปลาย Y สูงสุดของแท่งแนวนอน
        target_coordinate = bbox_target.min.y # ด้าน Y ต่ำสุดของแท่งแนวตั้ง
      # ถ้าแท่งแนวนอนอยู่ข้างหน้าของแท่งแนวตั้ง (ยืดไปทาง -Y)
      elsif (bbox_extend.min.y > bbox_target.max.y) && (bbox_extend.center.x.between?(bbox_target.min.x - tolerance, bbox_target.max.x + tolerance) || bbox_target.center.x.between?(bbox_extend.min.x - tolerance, bbox_extend.max.x + tolerance))
        extend_axis = 1 # Y-axis
        extend_direction_vector = Geom::Vector3d.new(0, -1, 0) # ไปทาง -Y
        current_coord_to_extend_from = bbox_extend.min.y # ปลาย Y ต่ำสุดของแท่งแนวนอน
        target_coordinate = bbox_target.max.y # ด้าน Y สูงสุดของแท่งแนวตั้ง
      else
        UI.messagebox("วัตถุไม่ได้อยู่ในตำแหน่งที่สามารถยืดไปชนกันในแนวราบได้ หรือชนกันอยู่แล้ว")
        model.abort_operation
        return
      end

      # 4. คำนวณระยะที่ต้อง Push/Pull
      pull_distance = target_coordinate - current_coord_to_extend_from
      
      # 5. ตรวจสอบว่ามีระยะที่ต้องปรับจริงๆ
      if pull_distance.abs < 0.0001 
          UI.messagebox("แท่งแนวนอนอยู่ตำแหน่งที่ชนแท่งแนวตั้งอยู่แล้ว ไม่จำเป็นต้องยืด")
          model.abort_operation
          return
      end

      # 6. หา Face ที่เป็น "ปลาย" ของแท่งแนวนอนตามทิศทางที่ต้องการยืด
      face_to_pull = nil
      # ต้องเข้าสู่ context ของ group/component ก่อนถึงจะเข้าถึง entities ได้อย่างถูกต้อง
      # และ bounding box ของ entity ใน context นี้จะเป็น local bounding box
      
      transform_extend = object_to_extend.transformation
      
      object_to_extend.definition.entities.each do |entity|
        if entity.is_a?(Sketchup::Face)
          # แปลง normal ของ face จาก local ไป global
          face_normal_global = transform_extend * entity.normal
          
          # ตรวจสอบว่า normal vector ของ Face ชี้ไปในทิศทางเดียวกับที่เราจะ Push/Pull (ใน Global)
          if face_normal_global.dot(extend_direction_vector) > 0.99
            # สร้าง BoundingBox ชั่วคราวจากจุดมุมของ Face ใน Global Coordinate
            face_points_global = entity.outer_loop.vertices.map { |v| v.position.transform(transform_extend) }
            face_bbox_global = Geom::BoundingBox.new
            face_bbox_global.add(face_points_global)
            
            # ตรวจสอบว่า Face นั้นอยู่ปลายสุดในทิศทางนั้นๆ ใน Global Coordinate
            if extend_axis == 0 # X-axis
              if extend_direction_vector.x > 0 # ยืดไป +X
                if face_to_pull.nil? || face_bbox_global.max.x > face_to_pull.bounds.transform(transform_extend).max.x
                  face_to_pull = entity
                end
              else # ยืดไป -X
                if face_to_pull.nil? || face_bbox_global.min.x < face_to_pull.bounds.transform(transform_extend).min.x
                  face_to_pull = entity
                end
              end
            elsif extend_axis == 1 # Y-axis
              if extend_direction_vector.y > 0 # ยืดไป +Y
                if face_to_pull.nil? || face_bbox_global.max.y > face_to_pull.bounds.transform(transform_extend).max.y
                  face_to_pull = entity
                end
              else # ยืดไป -Y
                if face_to_pull.nil? || face_bbox_global.min.y < face_to_pull.bounds.transform(transform_extend).min.y
                  face_to_pull = entity
                end
              end
            end
          end
        end
      end


      if face_to_pull
        # การ Push/Pull บน Face ที่อยู่ใน Definition ของ Group/Component นั้น
        # ต้องทำใน Active Context
        
        # เก็บ Active Path ปัจจุบันไว้ เพื่อจะกู้คืนทีหลัง
        original_active_path = model.active_path
        
        # เข้าสู่โหมดแก้ไขของ object_to_extend
        model.active_path = original_active_path.nil? ? [object_to_extend] : original_active_path + [object_to_extend]

        # ตอนนี้ face_to_pull สามารถถูก Push/Pull ได้
        # ระยะทางที่ Push/Pull คือค่าสัมบูรณ์ของ pull_distance
        # ทิศทางจะถูกกำหนดโดย normal ของ face_to_pull เอง (ซึ่งเราเลือกมาให้ตรงกับ extend_direction_vector แล้ว)
        
        face_to_pull.pushpull(pull_distance.abs) 

        UI.messagebox("แท่งแนวนอนถูกยืดไปชนข้างแท่งแนวตั้งเรียบร้อยแล้ว!")

        # กู้คืน Active Path เดิม
        model.active_path = original_active_path

      else
        UI.messagebox("ไม่พบพื้นผิวที่เหมาะสมสำหรับแท่งแนวนอน (วัตถุชิ้นแรกที่เลือก) เพื่อทำการยืด กรุณาตรวจสอบว่าเป็นรูปทรงสี่เหลี่ยมธรรมดาและอยู่ในแนวแกนหลัก")
      end

    rescue => e
      UI.messagebox("เกิดข้อผิดพลาด: #{e.message}\n#{e.backtrace.join("\n")}")
    ensure
      model.commit_operation # จบ Operation ไม่ว่าจะสำเร็จหรือไม่ก็ตาม
    end
  end

  # เพิ่มคำสั่งเข้าสู่เมนู SketchUp
  unless file_loaded?(__FILE__)
    menu = UI.menu("Extensions") 
    if menu.nil?
      menu = UI.menu("Plugins")
    end

    if menu.nil?
      UI.messagebox("ไม่พบเมนู 'Extensions' หรือ 'Plugins' ใน SketchUp. กรุณาตรวจสอบการตั้งค่าภาษาของ SketchUp หรือเวอร์ชัน.")
    else
      menu.add_item("ยืดแท่งแนวนอนไปชนข้างแท่งแนวตั้ง") { self.extend_horizontal_to_vertical_side }
    end

    file_loaded(__FILE__)
  end

end # module