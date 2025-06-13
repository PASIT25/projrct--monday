# frozen_string_literal: true

# my_sketchup_tools_align_only_final.rb
# This file contains the combined code for "My SketchUp Tools" Extension,
# featuring only the "Align Boxes" functionality.

module MySketchUpTools
  # Extension Information
  EXTENSION_ID = 'MySketchUpTools.align_only_final'.freeze # Unique ID for this final version
  EXTENSION_NAME = 'เครื่องมือ SketchUp ของฉัน (ย้ายเท่านั้น)'.freeze # Specific name
  EXTENSION_VERSION = '1.0.2'.freeze # Updated version
  EXTENSION_DESCRIPTION = 'เครื่องมือสำหรับย้ายกล่องให้ชนกันใน SketchUp'.freeze
  EXTENSION_CREATOR = 'ชื่อผู้พัฒนาของคุณ'.freeze
  EXTENSION_COPYRIGHT = 'ลิขสิทธิ์ 2024'.freeze

  # ===========================================================================
  # ฟังก์ชัน: ชนกล่องอัตโนมัติ (ย้ายกล่องที่สองไปชนกล่องแรก)
  # ===========================================================================
  def self.align_boxes
    model = Sketchup.active_model
    selection = model.selection

    if selection.length != 2
      UI.messagebox('ชนกล่องอัตโนมัติ: โปรดเลือกกล่องสองกล่องที่ต้องการให้ชนกัน', MB_OK)
      return
    end

    box_a = selection[0]
    box_b = selection[1]

    unless (box_a.is_a?(Sketchup::Group) || box_a.is_a?(Sketchup::ComponentInstance)) &&
           (box_b.is_a?(Sketchup::Group) || box_b.is_a?(Sketchup::ComponentInstance))
      UI.messagebox('ชนกล่องอัตโนมัติ: โปรดเลือก Group หรือ ComponentInstance สองชิ้น', MB_OK)
      return
    end

    bounds_a = box_a.bounds
    bounds_b = box_b.bounds

    # Determine which box is the target (stationary) and which is moving.
    # Assumption: The taller box is the target.
    if bounds_a.height > bounds_b.height
      target_box = box_a
      moving_box = box_b
    else
      target_box = box_b
      moving_box = box_a
    end

    model.start_operation('ชนกล่องอัตโนมัติ', true)

    begin
      target_bounds = target_box.bounds
      moving_bounds = moving_box.bounds

      # Get the X-coordinate of the right edge of the target box.
      target_edge_x = target_bounds.max.x
      # Get the X-coordinate of the left edge of the moving box.
      moving_edge_x = moving_bounds.min.x

      # Calculate the distance needed to move the moving box.
      distance_to_move = target_edge_x - moving_edge_x

      # Create a translation vector (movement only along X-axis).
      translation_vector = Geom::Vector3d.new(distance_to_move, 0, 0)

      # Apply the transformation to the moving box.
      moving_box.transform!(Geom::Transformation.translation(translation_vector))

      model.commit_operation
      UI.messagebox('ชนกล่องอัตโนมัติ: ย้ายกล่องชนกันเรียบร้อยแล้ว!', MB_OK)

    rescue => e
      model.abort_operation
      UI.messagebox("ชนกล่องอัตโนมัติ: เกิดข้อผิดพลาด: #{e.message}", MB_OK)
    end
  end

  # ===========================================================================
  # การลงทะเบียน Extension และสร้าง UI (เมนู)
  # ===========================================================================

  # This block ensures the extension is registered and its UI is created only once.
  unless file_loaded?(__FILE__)

    # The SketchupExtension object needs a path to the main file, even if it's the same file.
    # We use __FILE__ to refer to this very file.
    @extension = SketchupExtension.new(EXTENSION_NAME, __FILE__)
    @extension.description = EXTENSION_DESCRIPTION
    @extension.version = EXTENSION_VERSION
    @extension.creator = EXTENSION_CREATOR
    @extension.copyright = EXTENSION_COPYRIGHT
    Sketchup.register_extension(@extension, true) # Register and load on startup

    # --- UI Creation ---
    extensions_menu = UI.menu('Extensions')
    # Create the main submenu for our extension
    extension_menu = extensions_menu.add_submenu(EXTENSION_NAME)

    # Add "ชนกล่องอัตโนมัติ" command
    cmd_align = UI::Command.new('ชนกล่องอัตโนมัติ') { self.align_boxes }
    cmd_align.tooltip = 'ย้ายกล่องที่สองไปชนกับกล่องแรก'
    extension_menu.add_item(cmd_align)

    # Mark this file as loaded to prevent duplicate UI creation on reload.
    file_loaded(__FILE__)
  end

end # End of module MySketchUpTools