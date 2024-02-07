from mcu import MCU_endstop

class ZCalHelper:
    def __init__(self, config):
        self.printer = config.get_printer()
        self.printer.register_event_handler("klippy:connect",
                                            self.handle_connect)
        self.printer.register_event_handler("homing:home_rails_end",
                                            self.handle_home_rails_end)
        self.gcode = self.printer.lookup_object('gcode')
        self.gcode.register_command('CAL_Z', self.cmd_CAL_Z,
                                    desc=self.cmd_CAL_Z_help)
        self.gcode_move = self.printer.lookup_object('gcode_move')
    def handle_connect(self):
        pass
    def handle_home_rails_end(self, homing_state, rails):
        pass
    cmd_CAL_Z_help = ("Cal z")
    def cmd_CAL_Z(self, gcmd):
        layer = 0
        gcmd.respond_info("start Cal z")
        probe_zero = self._probe_z(gcmd)
        gcmd.respond_info("probe_zero: %.4f" % probe_zero)
        offset = probe_zero + layer
        self._set_new_gcode_offset(offset)
        gcmd.respond_info("end Cal z, offset: %.4f" % offset)
    def _probe_z(self, gcmd):
        tolerance = 0.04
        t_retries = 5
        samples = 3
        retries = 0
        positions = []
        while len(positions) < samples:
            curpos = self._probe()
            gcmd.respond_info("probe: %.4f" % curpos[2])
            positions.append(curpos[:3])
            z_positions = [p[2] for p in positions]
            if max(z_positions) - min(z_positions) > tolerance:
                if retries >= t_retries:
                    raise gcmd.error("Probe samples exceed tolerance")
                gcmd.respond_info("Probe samples exceed tolerance."
                                       " Retrying...")
                retries += 1
                positions = []
        return self._calc_mean(positions)[2]
    def _set_new_gcode_offset(self, offset):
        gcmd_offset = self.gcode.create_gcode_command("SET_GCODE_OFFSET",
                                                      "SET_GCODE_OFFSET",
                                                      {'Z': offset})
        self.gcode_move.cmd_SET_GCODE_OFFSET(gcmd_offset)
    def _probe(self):
        position_min = -5
        speed = 25
        lift_speed = 25
        retract_dist = 20
        x = 140
        y = 90
        toolhead = self.printer.lookup_object('toolhead')
        pos = toolhead.get_position()
        pos[2] = position_min
        phoming = self.printer.lookup_object('homing')
        probe = self.printer.lookup_object('probe')
        self._move([x, y, None], lift_speed)
        curpos = phoming.probing_move(probe.mcu_probe, pos, speed)
        self._move([None, None, curpos[2] + retract_dist], lift_speed)
        return curpos
    def _move(self, coord, speed):
        self.printer.lookup_object('toolhead').manual_move(coord, speed)
    def _calc_mean(self, positions):
        count = float(len(positions))
        return [sum([pos[i] for pos in positions]) / count
                for i in range(3)]
def load_config(config):
    return ZCalHelper(config)
